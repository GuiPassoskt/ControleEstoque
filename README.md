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