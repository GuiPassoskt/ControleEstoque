GitHub Copilot Chat Assistant

# ControleEstoque

Sistema front‑end para controle de estoque, cadastro de produtos, gerenciamento de personagens (atributos) e visualização de histórico. Projeto single‑user que usa localStorage do navegador como persistência (sem backend).

## Tecnologias
- HTML5, CSS (inline)
- JavaScript (jQuery)
- Bootstrap (via CDN)
- Armazenamento: localStorage do navegador

## Estado
- Repositório: https://github.com/GuiPassoskt/ControleEstoque
- Licença: MIT

---

## Estrutura principal do repositório
- `index.html` — painel principal / menu
- `produto.html` — formulário de cadastro de produto e gestão de personagens
- `estoque.html` — visualização e filtros do inventário (agregação por atributos)
- Outras páginas: `venda.html`, `cliente.html`, `historicovendas.html`, `realizardevolução.html`, `historicodevolução.html`, `pedidosclientes.html`, `carrinhocompras.html`, `carrinhodevolucoes.html`, `gerenciamentocliente.html`, `valores.html`, `historico.html`
- `LICENSE`, `.gitattributes`, `.github` — metadados

---

## Como executar (local)
1. Clone o repositório ou baixe os arquivos.
2. Abra `index.html` em um navegador moderno (Chrome, Firefox, Edge).
   - Não é necessário servidor; tudo roda como páginas estáticas.
3. Observação offline: o projeto carrega dependências via CDN (Bootstrap, jQuery, Popper, Font Awesome). Para uso 100% offline, baixe essas dependências e atualize os links para arquivos locais.

---

## Modelo de dados (exemplo)
Cada produto é um objeto com esta estrutura:
```json
{
  "registro": 1,
  "nome": "Mochila Infantil",
  "tipo": "Costa",
  "personagem": "Herói",
  "quantidade": 10,
  "tamanho": "M",
  "reparticoes": "2",
  "horaClique": "01/07/2024 14:23:01"
}
```

Chaves usadas no localStorage:
- `produtos` — array dos produtos cadastrados (fonte do estoque)
- `historico` — array com histórico de cadastros
- `personagens` — array com personagens personalizados
- `reparticaoSelecionada` — valor da última repartição selecionada

Para inspecionar no console do navegador:
```js
JSON.parse(localStorage.getItem('produtos'))
```

---

## Funcionalidades principais (fluxo do usuário)
1. Cadastro de produto (`produto.html`)
   - Preenchimento de nome, tipo, personagem, quantidade, tamanho e repartições.
   - Adicionar personagem customizado (botão "Adicionar" → input → blur adiciona ao seletor e salva em `personagens`).
   - Ao submeter: produto é salvo em `produtos` e também adicionado a `historico`; `registro` é atribuído como `produtos.length + 1`.

2. Visualizar estoque (`estoque.html`)
   - `carregarEstoque()` lê `produtos`, agrupa por chave (concatenação de campos) e soma quantidades.
   - Tabela: NOME | TIPO | PERSONAGEM | TAM | DIVISÕES | TOTAL
   - Cores no TOTAL:
     - vermelho: quantidade <= 20
     - amarelo: quantidade <= 50
     - verde: quantidade > 50

3. Filtragem
   - Dropdowns para Nome, Tipo, Personagem, Tamanho e Repartições ocultam/mostram linhas com base em conteúdo das células.

4. Histórico
   - Ao cadastrar, o item também entra em `historico`. Páginas de histórico leem dessa chave.

---

## Limitations / Observações técnicas
- Persistência local (localStorage): dados ficam apenas no navegador/maquina do usuário; sem sincronização multi‑usuário.
- Concorrência: múltiplas abas podem causar condições de corrida ao manipular localStorage.
- `registro` calculado como `produtos.length + 1`: remoções/edições podem causar duplicidade ou reatribuição de registros.
- Agrupamento por concatenação simples dos campos: pode gerar colisões se os valores forem ambíguos; não há normalização de case/trim antes da agregação.
- Validações: básicas (required/min). Falta validação e saneamento mais robustos.
- A cor `amarelo` (color: yellow) pode ter baixo contraste em fundo claro — problema de acessibilidade.
- Dependência de CDNs: sem internet, recursos externos (Bootstrap, jQuery, Font Awesome) podem falhar.

---

## Boas práticas e recomendações de melhorias
Prioridade alta
- Implementar backend (API REST) para persistência centralizada e multiusuário.
- Usar IDs únicos (UUID) para cada produto em vez de `produtos.length + 1`.
- Implementar edição e remoção de produtos, com atualização consistente do histórico.
- Substituir concatenação por chave robusta (ex.: normalizar campos + hashing/JSON.stringify).

Média
- Normalizar inputs (trim, toLowerCase) antes de salvar/fazer comparações.
- Melhorar contraste e acessibilidade (WCAG) — trocar amarelo por cor de alto contraste.
- Substituir alert() por feedbacks inline e modais para melhor UX.

Baixa
- Migrar jQuery para JavaScript moderno (vanilla).
- Adicionar testes unitários para funções de agregação e manipulação de localStorage.
- Implementar export/import (CSV/JSON).

---

## Exemplo de fluxo de teste rápido
1. Abra `produto.html`.
2. Cadastre um produto com quantidade 10.
3. Abra `estoque.html` — verifique que o produto aparece e o total esteja na cor apropriada (vermelho se <=20).
4. No console: `JSON.parse(localStorage.getItem('historico'))` para ver a entrada no histórico.

---

## Contribuindo
1. Fork do repositório.
2. Crie uma branch com a feature ou correção: `git checkout -b feature/nome-da-feature`
3. Faça commits pequenos e descreva bem.
4. Abra Pull Request com descrição das mudanças e instruções de teste.
5. Sugestões de PRs úteis:
   - `feature/uuid-product-ids` — substituir `registro` por UUID.
   - `fix/contrast-colors` — ajustar cor amarela para melhor contraste.
   - `feature/edit-delete-products` — UI + lógica para editar/remover produtos.

---

## Licença
Este projeto está licenciado sob a MIT License — ver arquivo `LICENSE` no repositório.

---

Se quiser, eu:
- gero este README.md como arquivo pronto para push,
- ou crio um PR com qualquer melhoria listada (ex.: substituição de `registro` por UUID ou correção da cor amarela). Indique qual opção prefere.

PLANO DE MIGRAÇÃO QUASAR FRAMEWORK

Perfeito — posso fazer a migração completa para Quasar (última versão) seguindo boas práticas. Antes de começar a gerar arquivos/commitar, preciso confirmar algumas opções para adaptar a conversão ao seu fluxo desejado.

Responda rapidamente a estas perguntas (bastam respostas curtas):
1. Quer que o projeto use TypeScript (recomendado) ou JavaScript puro?  
2. Persistência: manter localStorage (simples) ou trocar para localForage/IndexedDB (assíncrono e mais robusto)? ou já prefere preparar para um backend REST (placeholder + mock)?  
3. Deseja que eu crie os arquivos e te mostre o diff/zip aqui, ou que eu crie/mande commits direto no seu repositório (faço um branch e abro PR)? Se for commit/PR preciso que confirme o repositório alvo (owner é GuiPassoskt — confirme) e permissão para escrever.  
4. Quer que eu aplique também ESLint + Prettier + Husky + lint-staged e configuração básica de testes (Vitest), ou prefere só o mínimo funcional?

Enquanto você responde, segue um plano de migração, arquitetura proposta e exemplos de implementação (componentes e serviços) para você avaliar.

Resumo da abordagem (boas práticas)
- Quasar + Vue 3 (Composition API) + Vite (config padrão do Quasar).
- State management: Pinia (persistido via plugin + localForage).
- Serviços: camada de services (ProductService) para isolar acesso ao storage e regras de negócio.
- Roteamento: Vue Router (rotas para Home, Produtos, Estoque, Vendas, Histórico, Clientes, Devoluções).
- Componentização: cada página como componente Quasar Page; componentes menores (ProductForm, ProductTable, Filters).
- Tipagem: TypeScript recomendado (tipos para Product, Filtragem, etc).
- Qualidade: ESLint + Prettier; estrutura de commits padrões; testes básicos (Vitest) opcionais.
- Acessibilidade e contraste: evitar amarelo puro em texto; usar cores com contraste (ex.: amber-7) e indicadores adicionais (ícones/etiquetas).
- Persistência: localForage (async) com fallback para localStorage; possiblidade de trocar por API no futuro.

Proposta de estrutura do projeto
- src/
  - boot/ (arquivos boot do Quasar, ex: pinia, storage)
  - components/
    - ProductForm.vue
    - ProductTable.vue
    - CharacterManager.vue
  - pages/
    - Index.vue (Home)
    - ProductPage.vue
    - StockPage.vue
    - SalePage.vue
    - HistoryPage.vue
    - ClientPage.vue
    - ReturnPage.vue
  - layouts/
    - MainLayout.vue (QLayout com header/drawer)
  - router/
    - index.ts
  - stores/
    - products.ts (Pinia)
  - services/
    - storage.service.ts (localForage wrapper)
    - product.service.ts (business API)
  - boot/
    - pinia.ts
    - storage.ts
  - assets/
  - App.vue
  - main.ts
- quasar.conf.js (ou quasar.config.ts)
- package.json
- .eslintrc.cjs, .prettierrc

Comandos iniciais para scaffold
(assumindo Node >= 18, npm ou pnpm)

- Instalar Quasar CLI + criar projeto
  - npm i -g @quasar/cli
  - quasar create controle-estoque-quasar
  - Escolher: Vue 3, Vite, TypeScript? (conforme sua escolha), Pinia, ESLint, Prettier.

Dependências adicionais sugeridas
- pinia-plugin-persistedstate (ou pinia-plugin-persist)
- localforage
- uuid (para ids)
- @quasar/extras (opcional)
- vitest (opcional)

Exemplos de código (esqueleto) — adaptáveis para TypeScript ou JS

1) model/types (TypeScript)
src/types/product.ts
```ts
export interface Product {
  id: string;           // UUID
  name: string;
  type: string;
  character?: string;
  quantity: number;
  size?: string;
  partitions?: string;
  createdAt: string;    // ISO
}
```

2) storage.service.ts (usa localForage)
src/services/storage.service.ts
```ts
import localforage from 'localforage';

localforage.config({
  name: 'controle-estoque',
  storeName: 'controle_estoque_store'
});

export const StorageService = {
  async get<T>(key: string): Promise<T | null> {
    const v = await localforage.getItem<T>(key);
    return v ?? null;
  },
  async set<T>(key: string, payload: T): Promise<void> {
    await localforage.setItem<T>(key, payload);
  },
  async remove(key: string) {
    await localforage.removeItem(key);
  }
};
```

3) product.service.ts
src/services/product.service.ts
```ts
import { v4 as uuidv4 } from 'uuid';
import { StorageService } from './storage.service';
import type { Product } from 'src/types/product';

const PROD_KEY = 'produtos';
const HIST_KEY = 'historico';
const CHAR_KEY = 'personagens';

export const ProductService = {
  async list(): Promise<Product[]> {
    return (await StorageService.get<Product[]>(PROD_KEY)) ?? [];
  },

  async create(payload: Omit<Product,'id'|'createdAt'>) {
    const produtos = (await this.list()) ?? [];
    const product: Product = {
      ...payload,
      id: uuidv4(),
      createdAt: new Date().toISOString()
    };
    produtos.push(product);
    await StorageService.set(PROD_KEY, produtos);

    // append to history
    const h = (await StorageService.get<Product[]>(HIST_KEY)) ?? [];
    h.push(product);
    await StorageService.set(HIST_KEY, h);

    return product;
  },

  async aggregate(): Promise<Array<{ key: string; data: Product & { total: number } }>> {
    const produtos = await this.list();
    const map = new Map<string, { product: Product; total: number }>();
    for (const p of produtos) {
      const chave = `${p.name}|${p.type}|${p.size ?? ''}|${p.partitions ?? ''}|${p.character ?? ''}`.toLowerCase();
      if (!map.has(chave)) map.set(chave, { product: p, total: p.quantity });
      else map.get(chave)!.total += p.quantity;
    }
    return Array.from(map.entries()).map(([key, val]) => ({ key, data: { ...val.product, quantity: val.total }, total: val.total }));
  },

  // more methods: update, delete, listCharacters, addCharacter, removeCharacter...
};
```

4) Pinia store (stores/products.ts)
src/stores/products.ts
```ts
import { defineStore } from 'pinia';
import { ProductService } from 'src/services/product.service';
import type { Product } from 'src/types/product';

export const useProductsStore = defineStore('products', {
  state: () => ({
    products: [] as Product[],
    characters: [] as string[]
  }),
  actions: {
    async fetchAll() {
      this.products = await ProductService.list();
    },
    async createProduct(payload: Omit<Product,'id'|'createdAt'>) {
      const p = await ProductService.create(payload);
      this.products.push(p);
    },
    async loadCharacters() {
      const c = (await ProductService.listCharacters()) ?? [];
      this.characters = c;
    },
    // update/delete...
  }
});
```

5) Page de Product (exemplo simplificado usando Quasar components)
src/pages/ProductPage.vue
```vue
<template>
  <q-page padding>
    <q-card>
      <q-card-section>
        <div class="text-h6">Cadastro de Produto</div>
      </q-card-section>

      <q-form @submit.prevent="onSubmit" ref="formRef">
        <q-card-section>
          <q-select v-model="form.name" :options="nameOptions" label="Nome do Produto" dense required />
          <q-select v-model="form.type" :options="typeOptions" label="Tipo" dense required />
          <q-select v-model="form.character" :options="characters" label="Personagem" dense />
          <q-input v-model.number="form.quantity" type="number" label="Quantidade" dense min="1" required />
          <q-select v-model="form.size" :options="['P','M','G','Outro']" label="Tamanho" dense />
          <q-select v-model="form.partitions" :options="['1','2','3','4','5','Outro']" label="Repartições" dense />
        </q-card-section>

        <q-card-actions align="right">
          <q-btn label="Adicionar Produto" type="submit" color="primary" />
        </q-card-actions>
      </q-form>
    </q-card>
  </q-page>
</template>

<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { useProductsStore } from 'src/stores/products';

const store = useProductsStore();
const formRef = ref();
const form = ref({
  name: '',
  type: '',
  character: '',
  quantity: 1,
  size: '',
  partitions: ''
});

const nameOptions = ['Mochila Infantil','Mochila Juvenil','Mochila Company','Lancheira','Porta lápis'];
const typeOptions = ['Costa','Carrinho','Furada','Outro'];

onMounted(async () => {
  await store.fetchAll();
  await store.loadCharacters();
});

const characters = computed(() => store.characters);

async function onSubmit() {
  await store.createProduct({ 
    name: form.value.name,
    type: form.value.type,
    character: form.value.character,
    quantity: Number(form.value.quantity),
    size: form.value.size,
    partitions: form.value.partitions
  });
  // feedback
  $q.notify({ type: 'positive', message: 'Produto cadastrado com sucesso' });
  form.value = { name:'', type:'', character:'', quantity:1, size:'', partitions:''};
}
</script>
```

6) Page de Estoque (StockPage.vue) — usar QTable com agregação e filtros
- QTable com colunas: Nome, Tipo, Personagem, TAM, DIVISÕES, TOTAL
- Aplicar classes condicionais: use Quasar color classes (text-negative, text-warning, text-positive) ou chips.

Observações de UX/A11y
- Evitar usar apenas cor para comunicar alerta; incluir ícone ou badge.
- Usar validação de formulário (compon. Quasar) e mensagens claras.
- Contraste: não usar amarelo puro no texto; use amber/amber-8 ou text-warning e background adequado.

Checklist de configuração (CI/Qualidade)
- ESLint (config Quasar + TypeScript) + Prettier.
- Husky + lint-staged para pre-commit.
- Configurar GitHub Actions (opcional) para: lint, build, test.
- Adicionar README atualizado (vou gerar) e instruções de execução.

Próximos passos — o que você quer que eu faça agora?
- Opção A: Eu gero todo o esqueleto do projeto Quasar (arquivos + componentes + services) aqui e mostro o patch/zip para você aplicar localmente. (Preciso saber JS/TS e persistência)
- Opção B: Eu crio os arquivos e faço commits direto no repositório GuiPassoskt/ControleEstoque (crio branch + push). Para isso preciso da sua confirmação (owner) para usar githubwrite e que confirme o owner/repo alvo.
- Opção C: Eu te passo passo-a-passo detalhado (comandos) para você mesmo scaffoldar e migrar, incluindo todos os snippets acima e scripts de automação.

Diga qual opção prefere e responda às 4 perguntas no início (TypeScript? localForage? commit/PR? ESLint+Husky?). Assim começo a gerar o projeto imediatamente conforme sua escolha.

