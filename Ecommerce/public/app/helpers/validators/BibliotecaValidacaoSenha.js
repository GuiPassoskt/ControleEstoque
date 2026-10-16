/**
 *  Biblioteca de Validação de Senhas – XSS + Entidades HTML
 *  ------------------------------------------------------------
 *  Descrição:
 *  Biblioteca reutilizável para validação de senhas contra ataques
 *  XSS e entidades HTML malformadas. Exibe apenas mensagens via
 *  console.log e pode ser integrada em qualquer projeto JavaScript.
 *
 *  Autor: Guilherme Passos
 *  Versão: 1.0.0
 *  Criado em: 2025-09-29
 *  Última atualização: 2025-12-22
 *
 *  Licença: MIT
 *  SPDX-License-Identifier: MIT
 */

class BibliotecaValidacaoSenha {
  // Regex para entidades HTML
  #regexEntidadesCompletas = /&[a-zA-Z0-9]+;|&#\d+;|&#x[0-9A-Fa-f]+;/g;
  #regexEntidadesMalformadas = /&[a-zA-Z0-9]*(?![a-zA-Z0-9;])|&#\d*(?![0-9;])|&#x[0-9A-Fa-f]*(?![0-9A-Fa-f;])/g;
  #regexSuspeitasGeral = /&[^&\s]*(?:;|$)/g;

  // Caracteres XSS perigosos
  #caracteresXSS = {
    '<': { risco: 'alto', descricao: 'Abertura de tag HTML/XML' },
    '>': { risco: 'alto', descricao: 'Fechamento de tag HTML/XML' },
    '"': { risco: 'medio', descricao: 'Aspas duplas - escape de atributos' },
    "'": { risco: 'medio', descricao: 'Aspas simples - escape de atributos' },
    '&': { risco: 'medio', descricao: 'Início de entidade HTML' },
    '/': { risco: 'baixo', descricao: 'Barra - fechamento de tags' },
    '=': { risco: 'baixo', descricao: 'Igual - atribuição de valores' },
    '(': { risco: 'baixo', descricao: 'Parêntese - execução de funções' },
    ')': { risco: 'baixo', descricao: 'Parêntese - execução de funções' },
    ';': { risco: 'baixo', descricao: 'Ponto e vírgula - separador de comandos' },
    ':': { risco: 'baixo', descricao: 'Dois pontos - protocolos (javascript:)' },
    '`': { risco: 'medio', descricao: 'Crase - template literals em JS' },
    '|': { risco: 'baixo', descricao: 'Pipe - potencial uso em encadeamento ou filtros de template' },
  };

  // Entidades HTML específicas
  #entidadesEspecificas = {
    '&amp;': { caractere: '&', risco: 'medio', descricao: 'E comercial' },
    '&lt;': { caractere: '<', risco: 'alto', descricao: 'Menor que' },
    '&gt;': { caractere: '>', risco: 'alto', descricao: 'Maior que' },
    '&quot;': { caractere: '"', risco: 'medio', descricao: 'Aspas duplas' },
    '&apos;': { caractere: "'", risco: 'medio', descricao: 'Aspas simples' },
    '&nbsp;': { caractere: ' ', risco: 'baixo', descricao: 'Espaço não separável' },
    '&#35;': { caractere: '#', risco: 'baixo', descricao: 'Hash' },
    '&#x23;': { caractere: '#', risco: 'baixo', descricao: 'Hash hexadecimal' },
  };

  // Padrões XSS perigosos
  #padroesXSS = [
    { pattern: /<script/i, risco: 'critico', descricao: 'Tag script - execução de código' },
    { pattern: /javascript:/i, risco: 'critico', descricao: 'Protocolo javascript' },
    { pattern: /vbscript:/i, risco: 'critico', descricao: 'Protocolo vbscript' },
    { pattern: /on\w+=/i, risco: 'alto', descricao: 'Eventos HTML (onclick, onload, etc.)' },
    { pattern: /<iframe/i, risco: 'alto', descricao: 'Tag iframe' },
    { pattern: /<object/i, risco: 'alto', descricao: 'Tag object' },
    { pattern: /<embed/i, risco: 'alto', descricao: 'Tag embed' },
    { pattern: /<form/i, risco: 'medio', descricao: 'Tag form' },
    { pattern: /data:/i, risco: 'medio', descricao: 'Protocolo data' },
    { pattern: /expression\s*\(/i, risco: 'alto', descricao: 'CSS expression' },
  ];

  constructor(options = {}) {
    this.config = {
      logVerbose: options.logVerbose !== false, // Por padrão, ativa logs detalhados
      logPrefix: options.logPrefix || '🔐 [ValidacaoSenha]',
      logColors: options.logColors !== false, // Por padrão, usa cores no console
    };

    Object.freeze(this);

    this.#log('Biblioteca de validação inicializada', 'info');
  }

  /**
   * Valida senha contra XSS e entidades HTML
   * @param {string} senha - Senha a ser validada
   * @param {Object} options - Opções de validação
   * @returns {Object} Resultado da validação
   */
  validarSenha(senha, _options = {}) {
    const inicio = Date.now();

    if (!senha || typeof senha !== 'string') {
      this.#log('❌ Senha inválida ou vazia', 'error');
      return {
        valida: false,
        motivo: 'Senha inválida ou vazia',
        problemas: [],
        riscoGeral: 'erro',
        tempoValidacao: 0,
      };
    }

    this.#log(`🔍 Iniciando validação da senha: "${senha.substring(0, 10)}${senha.length > 10 ? '...' : ''}"`, 'info');

    // Executa todas as validações
    const entidadesHTML = this.#detectarEntidadesHTML(senha);
    const caracteresXSS = this.#detectarCaracteresXSS(senha);
    const padroesXSS = this.#detectarPadroesXSS(senha);

    // Combina todos os problemas
    const todosProblemas = [...entidadesHTML, ...caracteresXSS, ...padroesXSS];
    const riscoGeral = this.#calcularRiscoGeral(todosProblemas);
    const valida = todosProblemas.length === 0;

    const tempoValidacao = Date.now() - inicio;

    // Resultados para retorno
    const resultado = {
      valida,
      motivo: valida ? 'Senha válida' : this.#gerarMotivoRejeicao(todosProblemas, riscoGeral),
      problemas: todosProblemas,
      riscoGeral,
      estatisticas: {
        entidadesHTML: entidadesHTML.length,
        caracteresXSS: caracteresXSS.length,
        padroesXSS: padroesXSS.length,
        totalProblemas: todosProblemas.length,
      },
      tempoValidacao,
    };

    // Logs detalhados
    this.#logResultadoValidacao(resultado, senha);

    return resultado;
  }

  /**
   * Detecta entidades HTML na senha
   */
  #detectarEntidadesHTML(senha) {
    const problemas = [];

    // Entidades completas
    const entidadesCompletas = [...senha.matchAll(this.#regexEntidadesCompletas)];
    entidadesCompletas.forEach(match => {
      const entidade = match[0];
      const info = this.#entidadesEspecificas[entidade] || {
        caractere: 'desconhecido',
        risco: 'medio',
        descricao: 'Entidade HTML não catalogada',
      };

      problemas.push({
        tipo: 'entidade_completa',
        valor: entidade,
        posicao: match.index,
        risco: info.risco,
        descricao: info.descricao,
        categoria: 'html',
      });
    });

    // Entidades malformadas
    const entidadesMalformadas = [...senha.matchAll(this.#regexSuspeitasGeral)];
    entidadesMalformadas.forEach(match => {
      if (!entidadesCompletas.some(completa => completa.index === match.index) && match[0].length > 1) {
        problemas.push({
          tipo: 'entidade_malformada',
          valor: match[0],
          posicao: match.index,
          risco: 'medio',
          descricao: 'Entidade HTML malformada - possível bypass',
          categoria: 'html',
        });
      }
    });

    return problemas;
  }

  /**
   * Detecta caracteres XSS perigosos
   */
  #detectarCaracteresXSS(senha) {
    const problemas = [];

    for (let i = 0; i < senha.length; i++) {
      const char = senha[i];
      if (this.#caracteresXSS[char]) {
        const info = this.#caracteresXSS[char];
        problemas.push({
          tipo: 'caractere_xss',
          valor: char,
          posicao: i,
          risco: info.risco,
          descricao: info.descricao,
          categoria: 'xss',
        });
      }
    }

    return problemas;
  }

  /**
   * Detecta padrões XSS conhecidos
   */
  #detectarPadroesXSS(senha) {
    const problemas = [];

    this.#padroesXSS.forEach(padrao => {
      const matches = [...senha.matchAll(new RegExp(padrao.pattern.source, padrao.pattern.flags + 'g'))];
      matches.forEach(match => {
        problemas.push({
          tipo: 'padrao_xss',
          valor: match[0],
          posicao: match.index,
          risco: padrao.risco,
          descricao: padrao.descricao,
          categoria: 'xss',
        });
      });
    });

    return problemas;
  }

  /**
   * Calcula o risco geral baseado nos problemas
   */
  #calcularRiscoGeral(problemas) {
    if (problemas.length === 0) {
      return 'nenhum';
    }

    const riscos = problemas.map(p => p.risco);
    const riscoStrategy = {
      critico: 'critico',
      alto: 'alto',
      medio: 'medio',
      baixo: 'baixo',
    };
    return riscoStrategy[riscos[0]] || 'baixo';
  }

  /**
   * Gera motivo da rejeição
   */
  #gerarMotivoRejeicao(problemas, riscoGeral) {
    const contadores = problemas.reduce((acc, p) => {
      acc[p.categoria] = (acc[p.categoria] || 0) + 1;
      return acc;
    }, {});

    const motivos = [];
    if (contadores.html) {
      motivos.push(`${contadores.html} problema(s) de entidades HTML`);
    }
    if (contadores.xss) {
      motivos.push(`${contadores.xss} problema(s) de XSS`);
    }

    return `Risco ${riscoGeral.toUpperCase()}: ${motivos.join(', ')}`;
  }

  /**
   * Log do resultado da validação
   */
  #logResultadoValidacao(resultado, senha) {
    const { valida, riscoGeral, estatisticas, tempoValidacao } = resultado;

    if (valida) {
      this.#log(`✅ Senha VÁLIDA - Nenhum problema detectado (${tempoValidacao}ms)`, 'success');
    } else {
      this.#log(`❌ Senha REJEITADA - Risco: ${riscoGeral.toUpperCase()}`, 'error');
      this.#log(
        `📊 Estatísticas: ${estatisticas.totalProblemas} problemas (HTML: ${estatisticas.entidadesHTML}, XSS: ${estatisticas.caracteresXSS}, Padrões: ${estatisticas.padroesXSS})`,
        'warn'
      );

      if (this.config.logVerbose) {
        resultado.problemas.forEach((problema, index) => {
          this.#log(
            `   ${index + 1}. [${problema.risco.toUpperCase()}] "${problema.valor}" pos:${problema.posicao} - ${
              problema.descricao
            }`,
            'warn'
          );
        });
      }
    }
  }

  /**
   * Sistema de log personalizado
   */
  #log(mensagem, tipo = 'info') {
    if (!this.config.logVerbose && tipo === 'info') {
      return;
    }

    const timestamp = new Date().toLocaleTimeString();
    const prefix = this.config.logPrefix;

    if (this.config.logColors && typeof globalThis !== 'undefined') {
      // Logs coloridos para browser
      const cores = {
        info: 'color: #2196F3',
        success: 'color: #4CAF50; font-weight: bold',
        warn: 'color: #FF9800',
        error: 'color: #F44336; font-weight: bold',
      };
      console.log(`%c${prefix} [${timestamp}] ${mensagem}`, cores[tipo] || cores.info);
    } else {
      // Logs simples para Node.js ou quando cores estão desabilitadas
      const prefixos = {
        info: 'ℹ️',
        success: '✅',
        warn: '⚠️',
        error: '❌',
      };
      console.log(`${prefixos[tipo]} ${prefix} [${timestamp}] ${mensagem}`);
    }
  }

  /**
   * Valida múltiplas senhas em lote
   */
  validarLote(senhas, options = {}) {
    this.#log(`🔄 Iniciando validação em lote de ${senhas.length} senhas`, 'info');

    const inicio = Date.now();
    const resultados = senhas.map((senha, index) => {
      this.#log(`📝 Validando senha ${index + 1}/${senhas.length}`, 'info');
      return this.validarSenha(senha, options);
    });

    const tempo = Date.now() - inicio;
    const validas = resultados.filter(r => r.valida).length;
    const invalidas = resultados.length - validas;

    this.#log(`📊 Lote concluído: ${validas} válidas, ${invalidas} rejeitadas (${tempo}ms total)`, 'info');

    return {
      resultados,
      resumo: {
        total: senhas.length,
        validas,
        invalidas,
        tempoTotal: tempo,
        tempoMedio: tempo / senhas.length,
      },
    };
  }

  /**
   * Obtém estatísticas da biblioteca
   */
  obterEstatisticas() {
    const stats = {
      versao: '1.0.0',
      caracteresXSSMonitorados: Object.keys(this.#caracteresXSS).length,
      entidadesHTMLCatalogadas: Object.keys(this.#entidadesEspecificas).length,
      padroesXSSConhecidos: this.padroesXSS.length,
      configuracao: this.config,
    };

    this.#log('📈 Estatísticas da biblioteca:', 'info');
    this.#log(`   - Caracteres XSS monitorados: ${stats.caracteresXSSMonitorados}`, 'info');
    this.#log(`   - Entidades HTML catalogadas: ${stats.entidadesHTMLCatalogadas}`, 'info');
    this.#log(`   - Padrões XSS conhecidos: ${stats.padroesXSSConhecidos}`, 'info');

    return stats;
  }

  /**
   * Configura opções da biblioteca
   */
  configurar(novasOpcoes) {
    this.config = { ...this.config, ...novasOpcoes };
    this.#log('⚙️ Configuração atualizada', 'info');
    return this.config;
  }
}

// Exportação para diferentes ambientes
if (typeof module !== 'undefined' && module.exports) {
  // Node.js
  module.exports = BibliotecaValidacaoSenha;
} else if (typeof globalThis !== 'undefined') {
  // Browser
  globalThis.BibliotecaValidacaoSenha = BibliotecaValidacaoSenha;
}

export { BibliotecaValidacaoSenha };
