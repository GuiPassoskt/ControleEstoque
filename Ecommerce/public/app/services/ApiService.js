import { Logger } from '../helpers/logs/Logger.js';
import { Csrf } from '../helpers/http/Csrf.js';

export class ApiService {
  constructor(baseUrl = '') {
    this.baseUrl = baseUrl;
    this.logger = new Logger({ logVerbose: false });
    Object.freeze(this);
  }

  async get(url, params = null) {
    try {
      const response = await fetch(this.#montarUrl(url, params), {
        method: 'get',
        headers: this.#getHeaders(),
      });
      return await response.json();
    } catch (error) {
      this.logger.log(error, 'error');
    }
  }

  /**
   * GET que preserva o status HTTP: { ok, status, dados }.
   * Use quando a tela precisa distinguir erro de validação (422) de falha externa (5xx).
   */
  async getComStatus(url, params = null) {
    try {
      const response = await fetch(this.#montarUrl(url, params), {
        method: 'get',
        headers: this.#getHeaders(),
      });
      const dados = await response.json().catch(() => null);
      return { ok: response.ok, status: response.status, dados };
    } catch (error) {
      this.logger.log(error, 'error');
      return { ok: false, status: 0, dados: null };
    }
  }

  async post(url, data) {
    try {
      const response = await fetch(`${this.baseUrl}/${url}`, {
        method: 'post',
        headers: this.#getHeaders(),
        body: data,
      });
      return response.json();
    } catch (error) {
      this.logger.log(error, 'error');
    }
  }

  async put(url, data) {
    try {
      const response = await fetch(`${this.baseUrl}/${url}`, {
        method: 'put',
        headers: this.#getHeaders(),
        body: data,
      });
      return response.json();
    } catch (error) {
      this.logger.log(error, 'error');
    }
  }

  async delete(url) {
    try {
      const response = await fetch(`${this.baseUrl}/${url}`, {
        method: 'delete',
        headers: this.#getHeaders(),
      });
      return await response.json();
    } catch (error) {
      this.logger.log(error, 'error');
    }
  }

  #montarUrl(url, params) {
    const uri = new URL(`${this.baseUrl}/${url}`);
    if (params) {
      uri.search = new URLSearchParams(params);
    }
    return uri;
  }

  #getHeaders() {
    const token = localStorage.getItem('token');

    let headers = new Headers({
      //'Access-Control-Allow-Origin': '*',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
      ...Csrf.headers(),
    });

    if (token) {
      headers.set('Authorization', `Bearer ${token}`);
    }

    return headers;
  }
}
