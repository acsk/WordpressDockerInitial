/**
 * Google Apps Script para integração com WordPress WP Resgate
 * Versão 3.0 - Com Hash ID único e prevenção de duplicação
 * 
 * INSTRUÇÕES DE CONFIGURAÇÃO:
 * 1. Acesse script.google.com
 * 2. Crie um novo projeto
 * 3. Cole este código
 * 4. Configure a SHEET_ID na linha 12
 * 5. Publique como Web App
 * 6. Configure a URL do webhook no WordPress
 */

// CONFIGURAÇÃO - Altere para o ID da sua planilha
const SHEET_ID = '1ldFjuPTPQvxUBuAmPtyF_lUkX-6In0dJW4SX_l8QP9k';
const SHEET_NAME = 'Leads'; // Nome da aba

/**
 * Função principal que recebe os dados do WordPress
 */
function doPost(e) {
  try {
    Logger.log('=== WEBHOOK RECEBIDO ===');
    Logger.log('Event object:', e);
    Logger.log('PostData contents:', e.postData ? e.postData.contents : 'Sem postData');
    
    // Verificar se recebeu dados
    if (!e || !e.postData || !e.postData.contents) {
      throw new Error('Nenhum dado POST recebido');
    }
    
    // Parse dos dados recebidos
    const data = JSON.parse(e.postData.contents);
    
    Logger.log('Dados parseados:', JSON.stringify(data));
    Logger.log('Ação identificada:', data.action);
    
    // Verificar ação
    switch(data.action) {
      case 'new_lead':
        Logger.log('Processando NOVO LEAD');
        return handleNewLead(data);
        
      case 'update_lead':
        Logger.log('Processando ATUALIZAÇÃO DE LEAD');
        return handleUpdateLead(data);
        
      case 'delete_lead':
        Logger.log('Processando DELEÇÃO DE LEAD');
        return handleDeleteLead(data);
        
      default:
        Logger.log('Processando dados LEGADOS (sem action)');
        // Compatibilidade com versão anterior (dados sem action)
        return handleLegacyData(data);
    }
    
  } catch (error) {
    Logger.log('=== ERRO CRÍTICO NO WEBHOOK ===');
    Logger.log('Erro:', error.toString());
    Logger.log('Stack:', error.stack);
    Logger.log('Dados recebidos raw:', e ? JSON.stringify(e) : 'Event null');
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        message: 'Erro crítico no webhook: ' + error.message,
        error_type: error.name,
        timestamp: new Date().toISOString()
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Processar novo lead
 */
function handleNewLead(data) {
  try {
    const sheet = getOrCreateSheet();
    
    // Verificar se já existe (prevenir duplicação por hash_id)
    if (data.hash_id) {
      const existingRow = findRowByHashId(sheet, data.hash_id);
      if (existingRow > 0) {
        Logger.log('Lead já existe com hash_id:', data.hash_id);
        return ContentService
          .createTextOutput(JSON.stringify({
            success: true,
            message: 'Lead já existe - não duplicado por hash',
            lead_id: data.lead_id
          }))
          .setMimeType(ContentService.MimeType.JSON);
      }
    }
    
    // Verificação adicional por lead_id para prevenir duplicação
    if (data.lead_id) {
      const existingRowByLeadId = findRowByLeadId(sheet, data.lead_id);
      if (existingRowByLeadId > 0) {
        Logger.log('Lead já existe com lead_id:', data.lead_id);
        return ContentService
          .createTextOutput(JSON.stringify({
            success: true,
            message: 'Lead já existe - não duplicado por lead_id',
            lead_id: data.lead_id
          }))
          .setMimeType(ContentService.MimeType.JSON);
      }
    }
    
    // Preparar dados para inserção (13 colunas com hash_id)
    const rowData = [
      data.hash_id || '',           // A - Hash ID único
      data.lead_id || '',           // B - Lead ID do WordPress
      data.timestamp || new Date(), // C - Data/Hora
      data.name || '',              // D - Nome
      data.email || '',             // E - Email
      data.phone || '',             // F - Telefone
      data.website || '',           // G - Website
      data.problem_type || '',      // H - Tipo do Problema
      data.urgency || '',           // I - Urgência
      data.description || '',       // J - Descrição
      data.status || 'novo',        // K - Status
      data.created_at || data.timestamp || new Date(),  // L - Criado em
      data.updated_at || data.timestamp || new Date()   // M - Atualizado em
    ];
    
    // Inserir na planilha
    sheet.appendRow(rowData);
    
    // Formatar linha
    const lastRow = sheet.getLastRow();
    formatLeadRow(sheet, lastRow, data.urgency);
    
    Logger.log('Lead adicionado com sucesso:', data.email);
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Lead adicionado com sucesso',
        lead_id: data.lead_id,
        hash_id: data.hash_id
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    Logger.log('Erro ao adicionar lead:', error);
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        message: 'Erro ao adicionar lead: ' + error.message
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Processar atualização de lead
 */
function handleUpdateLead(data) {
  try {
    Logger.log('=== INICIANDO ATUALIZAÇÃO DE LEAD ===');
    Logger.log('Dados recebidos para atualização:', JSON.stringify(data));
    
    const sheet = getOrCreateSheet();
    const hashId = data.hash_id;
    
    if (!hashId) {
      throw new Error('Hash ID não fornecido para atualização');
    }
    
    Logger.log('Hash ID para busca:', hashId);
    
    // Encontrar a linha do lead pelo hash_id
    let rowIndex = findRowByHashId(sheet, hashId);
    
    if (rowIndex === -1) {
      Logger.log('Hash não encontrado, tentando buscar por lead_id:', data.lead_id);
      
      // Fallback: buscar por lead_id
      if (data.lead_id) {
        rowIndex = findRowByLeadId(sheet, data.lead_id);
        
        if (rowIndex > 0) {
          Logger.log('Lead encontrado por lead_id na linha:', rowIndex);
          
          // Atualizar o hash na planilha para sincronizar
          const updateHashRange = sheet.getRange(rowIndex, 1);
          updateHashRange.setValue(hashId);
          Logger.log('Hash atualizado na planilha para:', hashId);
        }
      }
      
      if (rowIndex === -1) {
        // Lead não encontrado nem por hash nem por lead_id, adicionar como novo
        Logger.log('Lead não encontrado nem por hash nem por lead_id, adicionando como novo');
        
        // Converter dados de atualização para novo lead
        const newLeadData = {
          ...data,
          action: 'new_lead',
          status: data.status || 'novo',
          created_at: data.updated_at || new Date(),
          timestamp: data.updated_at || new Date()
        };
        
        return handleNewLead(newLeadData);
      }
    }
    
    Logger.log('Lead encontrado na linha:', rowIndex);
    
    // Obter dados atuais da linha
    const range = sheet.getDataRange();
    const values = range.getValues();
    const currentRow = values[rowIndex - 1]; // -1 porque array é 0-indexed
    
    Logger.log('Dados atuais da linha:', currentRow);
    
    // Atualizar dados na linha encontrada (manter dados originais quando não fornecidos)
    const updatedRowData = [
      hashId,                                    // A - Hash ID (manter)
      data.lead_id || currentRow[1],            // B - Lead ID
      currentRow[2],                            // C - Manter timestamp original
      data.name || currentRow[3],               // D - Nome
      data.email || currentRow[4],              // E - Email
      data.phone || currentRow[5],              // F - Telefone
      data.website || currentRow[6],            // G - Website
      data.problem_type || currentRow[7],       // H - Tipo do Problema
      data.urgency || currentRow[8],            // I - Urgência (ATUALIZÁVEL)
      data.description || currentRow[9],        // J - Descrição
      data.status || currentRow[10],            // K - Status (ATUALIZÁVEL)
      currentRow[11],                           // L - Manter created_at original
      data.updated_at || new Date()             // M - Atualizado em
    ];
    
    Logger.log('Campos atualizáveis:');
    Logger.log('- Status:', data.status ? `${currentRow[10]} → ${data.status}` : 'sem alteração');
    Logger.log('- Urgência:', data.urgency ? `${currentRow[8]} → ${data.urgency}` : 'sem alteração');
    
    Logger.log('Dados para atualização:', updatedRowData);
    
    // Atualizar a linha
    const updateRange = sheet.getRange(rowIndex, 1, 1, updatedRowData.length);
    updateRange.setValues([updatedRowData]);
    
    Logger.log('Linha atualizada com sucesso');
    
    // Reformatar linha
    formatLeadRow(sheet, rowIndex, data.urgency || currentRow[8]);
    
    Logger.log('Formatação aplicada');
    Logger.log('=== ATUALIZAÇÃO CONCLUÍDA COM SUCESSO ===');
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Lead atualizado com sucesso',
        hash_id: hashId,
        row_index: rowIndex,
        updated_data: updatedRowData
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    Logger.log('=== ERRO NA ATUALIZAÇÃO ===');
    Logger.log('Erro completo:', error);
    Logger.log('Stack trace:', error.stack);
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        message: 'Erro ao atualizar lead: ' + error.message,
        error_details: error.toString(),
        hash_id: data.hash_id || 'não fornecido'
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Processar deleção de lead
 */
function handleDeleteLead(data) {
  try {
    const sheet = getOrCreateSheet();
    const hashId = data.hash_id;
    
    if (!hashId) {
      throw new Error('Hash ID não fornecido');
    }
    
    // Encontrar a linha do lead pelo hash_id
    let rowIndex = findRowByHashId(sheet, hashId);
    
    if (rowIndex === -1) {
      Logger.log('Hash não encontrado para deleção, tentando buscar por lead_id:', data.lead_id);
      
      // Fallback: buscar por lead_id
      if (data.lead_id) {
        rowIndex = findRowByLeadId(sheet, data.lead_id);
        
        if (rowIndex > 0) {
          Logger.log('Lead encontrado por lead_id para deleção na linha:', rowIndex);
        }
      }
      
      if (rowIndex === -1) {
        Logger.log('Lead não encontrado nem por hash nem por lead_id para deleção');
        return ContentService
          .createTextOutput(JSON.stringify({
            success: false,
            message: 'Lead não encontrado para deleção'
          }))
          .setMimeType(ContentService.MimeType.JSON);
      }
    }
    
    // Deletar a linha
    sheet.deleteRow(rowIndex);
    
    Logger.log('Lead deletado com sucesso:', hashId);
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Lead deletado com sucesso',
        hash_id: hashId
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    Logger.log('Erro ao deletar lead:', error);
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        message: 'Erro ao deletar lead: ' + error.message
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Compatibilidade com dados legados (versão anterior)
 */
function handleLegacyData(data) {
  try {
    const sheet = getOrCreateSheet();
    
    // Verificar se são dados no formato antigo (array values)
    if (data.values && Array.isArray(data.values) && data.values.length > 0) {
      const rowData = data.values[0];
      
      // Converter para novo formato
      const convertedData = {
        hash_id: 'LEGACY_' + new Date().getTime(), // Hash temporário
        lead_id: 0,
        timestamp: rowData[0] || new Date(),
        name: rowData[1] || '',
        email: rowData[2] || '',
        phone: rowData[3] || '',
        website: rowData[4] || '',
        problem_type: rowData[5] || '',
        urgency: rowData[6] || '',
        description: rowData[7] || '',
        status: 'novo',
        created_at: new Date(),
        updated_at: new Date()
      };
      
      return handleNewLead(convertedData);
    }
    
    throw new Error('Formato de dados não reconhecido');
    
  } catch (error) {
    Logger.log('Erro no processamento de dados legados:', error);
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        message: 'Erro no processamento: ' + error.message
      }))
      .setMimeType(ContentService.MimeType.JSON);
  }
}

/**
 * Encontrar linha pelo Hash ID
 */
function findRowByHashId(sheet, hashId) {
  Logger.log('Procurando hash_id:', hashId);
  
  const range = sheet.getDataRange();
  const values = range.getValues();
  
  Logger.log('Total de linhas na planilha:', values.length);
  Logger.log('Primeiras 3 linhas para debug:', values.slice(0, 3));
  
  // Procurar pelo hash_id (coluna A)
  for (let i = 1; i < values.length; i++) {
    const currentHashId = values[i][0];
    Logger.log(`Linha ${i + 1}: comparando '${currentHashId}' com '${hashId}'`);
    
    if (currentHashId === hashId) {
      Logger.log('Hash encontrado na linha:', i + 1);
      return i + 1; // +1 porque getRange é 1-indexed
    }
  }
  
  Logger.log('Hash não encontrado:', hashId);
  return -1; // Não encontrado
}

/**
 * Encontrar linha pelo Lead ID como fallback
 */
function findRowByLeadId(sheet, leadId) {
  Logger.log('Procurando por lead_id como fallback:', leadId);
  
  const range = sheet.getDataRange();
  const values = range.getValues();
  
  // Procurar pelo lead_id (coluna B)
  for (let i = 1; i < values.length; i++) {
    const currentLeadId = values[i][1];
    Logger.log(`Linha ${i + 1}: comparando lead_id '${currentLeadId}' com '${leadId}'`);
    
    if (currentLeadId == leadId) {
      Logger.log('Lead ID encontrado na linha:', i + 1);
      return i + 1; // +1 porque getRange é 1-indexed
    }
  }
  
  Logger.log('Lead ID não encontrado:', leadId);
  return -1; // Não encontrado
}

/**
 * Obter ou criar a planilha
 */
function getOrCreateSheet() {
  const spreadsheet = SpreadsheetApp.openById(SHEET_ID);
  let sheet = spreadsheet.getSheetByName(SHEET_NAME);
  
  if (!sheet) {
    // Criar nova aba se não existir
    sheet = spreadsheet.insertSheet(SHEET_NAME);
    
    // Criar cabeçalhos
    const headers = [
      'Hash ID',          // A - Identificador único
      'Lead ID',          // B - ID do WordPress
      'Data/Hora',        // C - Timestamp
      'Nome',             // D - Nome
      'Email',            // E - Email
      'Telefone',         // F - Telefone
      'Website',          // G - Website
      'Tipo do Problema', // H - Tipo do Problema
      'Urgência',         // I - Urgência
      'Descrição',        // J - Descrição
      'Status',           // K - Status
      'Criado em',        // L - Created at
      'Atualizado em'     // M - Updated at
    ];
    
    sheet.getRange(1, 1, 1, headers.length).setValues([headers]);
    
    // Formatar cabeçalhos
    const headerRange = sheet.getRange(1, 1, 1, headers.length);
    headerRange.setFontWeight('bold');
    headerRange.setBackground('#4285f4');
    headerRange.setFontColor('white');
    
    // Ajustar larguras das colunas
    sheet.setColumnWidth(1, 120); // Hash ID
    sheet.setColumnWidth(2, 80);  // Lead ID
    sheet.setColumnWidth(3, 150); // Data/Hora
    sheet.setColumnWidth(4, 200); // Nome
    sheet.setColumnWidth(5, 250); // Email
    sheet.setColumnWidth(6, 150); // Telefone
    sheet.setColumnWidth(7, 250); // Website
    sheet.setColumnWidth(8, 150); // Tipo
    sheet.setColumnWidth(9, 100); // Urgência
    sheet.setColumnWidth(10, 300); // Descrição
    sheet.setColumnWidth(11, 120); // Status
    sheet.setColumnWidth(12, 150); // Criado em
    sheet.setColumnWidth(13, 150); // Atualizado em
  }
  
  return sheet;
}

/**
 * Formatar linha do lead
 */
function formatLeadRow(sheet, rowIndex, urgency) {
  const dataRange = sheet.getRange(rowIndex, 1, 1, 13);
  
  // Cores alternadas
  if (rowIndex % 2 === 0) {
    dataRange.setBackground('#f8f9fa');
  }
  
  // Destacar urgência na coluna I (índice 9)
  const urgencyCell = sheet.getRange(rowIndex, 9);
  
  if (urgency === 'Crítica') {
    urgencyCell.setBackground('#ffebee');
    urgencyCell.setFontColor('#c62828');
    urgencyCell.setFontWeight('bold');
  } else if (urgency === 'Alta') {
    urgencyCell.setBackground('#fff3e0');
    urgencyCell.setFontColor('#ef6c00');
    urgencyCell.setFontWeight('bold');
  }
  
  // Destacar status na coluna K (índice 11)
  const statusCell = sheet.getRange(rowIndex, 11);
  const statusRange = sheet.getRange(rowIndex, 11, 1, 1);
  const status = statusRange.getValue();
  
  if (status === 'completed' || status === 'Concluído') {
    statusCell.setBackground('#e8f5e8');
    statusCell.setFontColor('#2e7d32');
  } else if (status === 'in_progress' || status === 'Em andamento') {
    statusCell.setBackground('#fff3e0');
    statusCell.setFontColor('#ef6c00');
  } else if (status === 'contacted' || status === 'Contatado') {
    statusCell.setBackground('#e3f2fd');
    statusCell.setFontColor('#1976d2');
  } else if (status === 'deleted') {
    // Marcar linha inteira como deletada
    const fullRow = sheet.getRange(rowIndex, 1, 1, 13);
    fullRow.setBackground('#f5f5f5');
    fullRow.setFontColor('#999999');
    statusCell.setBackground('#ffebee');
    statusCell.setFontColor('#d32f2f');
    statusCell.setFontWeight('bold');
  }
}

/**
 * Função de teste (para debug)
 */
function testFunction() {
  const testData = {
    action: 'new_lead',
    hash_id: 'WPR_teste123456',
    lead_id: 123,
    timestamp: new Date(),
    name: 'Teste WordPress Hash',
    email: 'teste@exemplo.com',
    phone: '(11) 99999-9999',
    website: 'https://exemplo.com',
    problem_type: 'Teste',
    urgency: 'Média',
    description: 'Teste de integração com Hash ID',
    status: 'novo',
    created_at: new Date(),
    updated_at: new Date()
  };
  
  const result = handleNewLead(testData);
  Logger.log('Resultado do teste:', result.getContent());
}

/**
 * Função de teste para atualização
 */
function testUpdate() {
  const testData = {
    action: 'update_lead',
    hash_id: 'WPR_teste123456', // Usar hash do teste anterior
    lead_id: 123,
    status: 'Em andamento',
    updated_at: new Date()
  };
  
  const result = handleUpdateLead(testData);
  Logger.log('Resultado da atualização:', result.getContent());
}

/**
 * Função para testar com hash real da planilha
 */
function testUpdateReal() {
  // Usar um dos hashes reais da sua planilha
  const testData = {
    action: 'update_lead',
    hash_id: 'WPR_bcfa9d0baa7a', // Hash real da planilha
    lead_id: 14,
    status: 'contacted', // Alterar para "contatado"
    updated_at: new Date()
  };
  
  Logger.log('Testando com hash real:', testData.hash_id);
  const result = handleUpdateLead(testData);
  Logger.log('Resultado da atualização real:', result.getContent());
}

/**
 * Função para listar todos os hashes da planilha
 */
function listAllHashes() {
  const sheet = getOrCreateSheet();
  const range = sheet.getDataRange();
  const values = range.getValues();
  
  Logger.log('=== TODOS OS HASHES NA PLANILHA ===');
  for (let i = 1; i < values.length; i++) {
    const hash = values[i][0];
    const leadId = values[i][1];
    const name = values[i][3];
    Logger.log(`Linha ${i + 1}: ${hash} | Lead ID: ${leadId} | Nome: ${name}`);
  }
}

/**
 * Função para limpar dados de teste
 */
function clearTestData() {
  const sheet = getOrCreateSheet();
  
  if (sheet) {
    // Manter apenas o cabeçalho (linha 1)
    const lastRow = sheet.getLastRow();
    if (lastRow > 1) {
      sheet.deleteRows(2, lastRow - 1);
      Logger.log('Dados de teste removidos');
    }
  }
}