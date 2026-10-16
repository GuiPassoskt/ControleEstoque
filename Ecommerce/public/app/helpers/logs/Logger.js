export class Logger {
  constructor(options = {}) {
    this.config = {
      logVerbose: options.logVerbose !== false, // Por padrão, ativa logs detalhados
      logPrefix: options.logPrefix || '📊 [Logger]',
      logColors: options.logColors !== false, // Por padrão, usa cores no console
    };
  }

  log(mensagem, tipo = 'info') {
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
      console.info(`%c${prefix} [${timestamp}] ${mensagem}`, cores[tipo] || cores.info);
    } else {
      // Logs simples para Node.js ou quando cores estão desabilitadas
      const prefixos = {
        info: 'ℹ️',
        success: '✅',
        warn: '⚠️',
        error: '❌',
      };
      console.info(`${prefixos[tipo]} ${prefix} [${timestamp}] ${mensagem}`);
    }
  }
}
