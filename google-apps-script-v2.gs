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
const SHEET_ID = 'SUA_SHEET_ID_AQUI';
const SHEET_NAME = 'Leads'; // Nome da aba

/**
 * Função principal que recebe os dados do WordPress
 */
function doPost(e) {
  try {
    // Parse dos dados recebidos
    const data = JSON.parse(e.postData.contents);
    
    Logger.log('Dados recebidos:', data);
    
    // Verificar ação
    switch(data.action) {
      case 'new_lead':
        return handleNewLead(data);
      case 'update_lead':
        return handleUpdateLead(data);
      case 'delete_lead':
        return handleDeleteLead(data);
      default:
        return handleNewLead(data); // Compatibilidade com versão anterior
    }
    
  } catch (error) {
    Logger.log('Erro no processamento:', error);
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        message: 'Erro no processamento: ' + error.message
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
            message: 'Lead já existe - não duplicado',
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
    
    Logger.log('Lead adicionado com sucesso:', data.email);
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Lead adicionado com sucesso',
        lead_id: data.lead_id
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
    const sheet = getOrCreateSheet();
    const hashId = data.hash_id;
    
    if (!hashId) {
      throw new Error('Hash ID não fornecido');
    }
    
    // Encontrar a linha do lead pelo hash_id
    let rowIndex = findRowByHashId(sheet, hashId);
    
    if (rowIndex === -1) {
      // Lead não encontrado, adicionar como novo
      Logger.log('Lead não encontrado pelo hash, adicionando como novo:', hashId);
      return handleNewLead(data);
    }
    
    // Obter dados atuais da linha
    const range = sheet.getDataRange();
    const values = range.getValues();
    const currentRow = values[rowIndex - 1]; // -1 porque array é 0-indexed
    
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
      data.urgency || currentRow[8],            // I - Urgência
      data.description || currentRow[9],        // J - Descrição
      data.status || currentRow[10],            // K - Status
      currentRow[11],                           // L - Manter created_at original
      data.updated_at || new Date()             // M - Atualizado em
    ];
    
    // Atualizar a linha
    const updateRange = sheet.getRange(rowIndex, 1, 1, updatedRowData.length);
    updateRange.setValues([updatedRowData]);
    
    Logger.log('Lead atualizado com sucesso:', leadId);
    
    return ContentService
      .createTextOutput(JSON.stringify({
        success: true,
        message: 'Lead atualizado com sucesso',
        lead_id: leadId
      }))
      .setMimeType(ContentService.MimeType.JSON);
      
  } catch (error) {
    Logger.log('Erro ao atualizar lead:', error);
    return ContentService
      .createTextOutput(JSON.stringify({
        success: false,
        message: 'Erro ao atualizar lead: ' + error.message
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
      Logger.log('Lead não encontrado para deleção:', hashId);
      return ContentService
        .createTextOutput(JSON.stringify({
          success: false,
          message: 'Lead não encontrado'
        }))
        .setMimeType(ContentService.MimeType.JSON);
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
 * Encontrar linha pelo Hash ID
 */
function findRowByHashId(sheet, hashId) {
  const range = sheet.getDataRange();
  const values = range.getValues();
  
  // Procurar pelo hash_id (coluna A)
  for (let i = 1; i < values.length; i++) {
    if (values[i][0] === hashId) {
      return i + 1; // +1 porque getRange é 1-indexed
    }
  }
  
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