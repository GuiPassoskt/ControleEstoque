# Checklist do e-commerce Utopia

Situação da implementação do plano descrito no [DOC.md](DOC.md): o que já foi feito, como foi verificado e o que ainda falta para colocar a loja no ar.

Legenda: `[x]` concluído e testado · `[ ]` pendente · ⚠️ precisa de atenção antes do deploy

---

## 1. Resumo

| Módulo do DOC.md | Situação |
| --- | --- |
| Produtos e estoque | Concluído |
| Pedidos e carrinho | Concluído (pagamento online depende das chaves do Mercado Pago) |
| Financeiro | Concluído |
| Integração Mercado Pago | Implementada, **não testada com chaves reais** |
| Integração Melhor Envio (cotação) | Implementada, **não testada com token real** |
| Melhor Envio (etiquetas) | Pendente |
| Deploy, SSL, SEO, backup | Pendente |
| Burocracia (MEI, conta PJ, NF) | Pendente (fora do código) |

---

## 2. O que foi feito

### 2.1 Produtos e estoque

- [x] CRUD completo de produtos no painel (`/admin/produtos`)
  - Campos novos: preço, peso (kg), altura, largura e comprimento (cm), usados na cotação de frete
  - Descrição passou de `TINYTEXT` (255 caracteres) para `TEXT`
  - Busca por nome ou SKU e filtro por categoria
  - Estoque com destaque: "Esgotado" (0) e alerta de estoque baixo (≤ 5 unidades)
- [x] Upload de imagem do produto
  - Aceita JPG, PNG e WEBP de até 2 MB
  - Salva em `public/uploads/produtos/` com nome aleatório
  - Fotos maiores que 1200px são redimensionadas automaticamente
  - Ao trocar a foto, a antiga é apagada
  - Pré-visualização da imagem antes de salvar
- [x] Validação no model: SKU único, preço maior que zero, estoque inteiro, categoria obrigatória
  - Testado: SKU duplicado é recusado e o formulário mantém os valores digitados
- [x] Exclusão de produto (exclusão lógica, `excluido_em`) com confirmação
  - Corrigido: o botão chamava uma função JS que só existia na página de categorias
- [x] Categorias: atualização corrigida (antes parava em `dd($id)`)
- [x] Seeder `ProdutoSeeder` com 6 mochilas de exemplo (`php spark db:seed ProdutoSeeder`)

### 2.2 Loja (vitrine) e carrinho

- [x] Vitrine em `/` com banner, vantagens, grade de produtos, ordenação (novidades, menor/maior preço, nome) e paginação
- [x] Menu de categorias e busca no cabeçalho (com versão para celular)
- [x] Página do produto (`/produto/{id}`) com seletor de quantidade, "Adicionar ao carrinho", "Comprar agora" e produtos relacionados
- [x] Carrinho na sessão (`/carrinho`)
  - Preço e estoque sempre lidos do banco, nunca da sessão
  - A quantidade é limitada ao estoque disponível
  - Produtos inativos ou esgotados saem do carrinho automaticamente
  - Contador de itens no ícone do cabeçalho

### 2.3 Checkout e pedidos

- [x] Checkout (`/checkout`) em 4 etapas: dados, endereço, entrega e pagamento
  - Máscaras de CEP e telefone
  - Endereço preenchido automaticamente pelo ViaCEP ao digitar o CEP
  - Cotação de frete ao digitar o CEP, com total atualizado na hora
  - A opção de frete escolhida é conferida no servidor contra a cotação guardada na sessão (o cliente não consegue alterar o valor)
  - Escolha entre Pix e cartão
- [x] Criação do pedido com status **Pendente**, em transação: cliente (localizado pelo e-mail ou cadastrado), endereço, pedido e itens
- [x] Página de acompanhamento do pedido (`/pedido/{id}`) com linha do tempo (recebido → pagamento → enviado)
  - Só quem fez o pedido naquela sessão consegue ver a página
  - Mostra o botão "Concluir pagamento" enquanto o pedido estiver Pendente
  - Mostra o código de rastreio quando o pedido for Enviado
- [x] Painel de pedidos (`/admin/pedidos`)
  - Filtro por status com contadores e busca por número, nome ou e-mail
  - Detalhe do pedido: itens, cliente (com link para o WhatsApp), endereço, pagamento e andamento
  - Ações: confirmar pagamento, marcar como enviado (com código de rastreio) e cancelar
- [x] Regras de estoque e financeiro ligadas ao pedido (`app/Libraries/PedidoLibrary.php`)
  - **Pagamento confirmado:** status Pago, baixa no estoque e lançamento de "Entrada" no financeiro
    - Testado: estoque 18 → 16 e entrada de R$ 404,80 registrada
  - **Cancelamento de pedido pago:** estoque devolvido e estorno lançado como "Saída"
    - Testado: estoque 16 → 18 e saída de R$ 404,80 registrada
  - A confirmação só age sobre pedido Pendente: se o webhook e o retorno do cliente chegarem juntos, o pagamento é processado uma única vez

### 2.4 Financeiro

- [x] Lançamentos (`/admin/financeiro`) com filtro por período e tipo, e totais de entradas, saídas e saldo
- [x] Lançamento manual de entradas e saídas (despesas, compra de estoque etc.)
- [x] Exclusão apenas de lançamentos manuais (os gerados por pedidos só mudam pelo próprio pedido)
- [x] Fluxo de caixa anual (`/admin/financeiro/fluxo-caixa`) com gráfico de entradas × saídas, saldo acumulado e tabela mês a mês
- [x] Dashboard do painel com números reais: pedidos aguardando pagamento, pedidos a enviar, entradas e saldo do mês, gráfico de vendas dos últimos 30 dias (líquidas de estornos), últimos pedidos e estoque baixo

### 2.5 Integrações

- [x] **Mercado Pago — Checkout Pro** (`app/Libraries/MercadoPagoLibrary.php`), pela API REST, sem dependência nova no Composer
  - Cria a preferência de pagamento e redireciona o cliente
  - Pix ou cartão (até 12x), conforme a escolha no checkout
  - Webhook em `POST /notificacoes/pagamento`: o pagamento é sempre reconsultado na API antes de alterar o pedido
  - Validação opcional da assinatura do webhook (`mercadopago.webhookSecret`)
  - Valor pago menor que o do pedido não confirma o pagamento
  - Reembolso ou chargeback cancela o pedido e lança o estorno
- [x] **Melhor Envio — cotação de frete** (`app/Libraries/MelhorEnvioLibrary.php`)
  - Usa peso e medidas de cada produto
  - Lista as opções do mais barato para o mais caro, com prazo
- [x] Funcionamento sem chaves (modo de desenvolvimento)
  - Sem token do Melhor Envio: frete fixo de R$ 25,00 e prazo de 7 dias (alteráveis no `.env`)
  - Sem token do Mercado Pago: pedido criado como Pendente e pagamento confirmado manualmente no painel

### 2.6 Telas

- [x] Loja com visual próprio em preto e branco, baseado no logo da Utopia, com laranja nos botões e preços
- [x] Painel mantém o AdminLTE, com menu Financeiro novo, item ativo destacado e link "Ver loja"
- [x] Responsivo: testado em 390px, sem rolagem horizontal e com barra de compra fixa na página do produto no celular
- [x] Mensagens de sucesso e erro em notificações (loja) e alertas (painel)

### 2.7 Mudanças no código existente

- [x] Proteção CSRF ligada no site inteiro (exceto o webhook); campo do token adicionado no login e no cadastro
- [x] Caminhos das views corrigidos para Linux: chamadas como `view('Admin\Estoque\Categorias')` só funcionavam no Windows
- [x] Fuso horário `America/Sao_Paulo` em `app/Config/App.php`
- [x] `index.php` removido das URLs (`$indexPage = ''`); o `.htaccess` já faz a reescrita
- [x] Template de paginação no padrão Bootstrap (`app/Views/Pagers/bootstrap.php`)
- [x] JavaScript em camadas (module → controller → service) seguindo o padrão de `public/app/`

### 2.8 Banco de dados

- [x] Migration `AdicionarCamposProduto`: preço, peso, altura, largura, comprimento e descrição em `TEXT`
- [x] Migration `AdicionarCamposPedido`: endereço, serviço de frete, código de rastreio e link de pagamento
- [x] Ambas já executadas no banco local
- [x] Dados de teste removidos (usuário temporário, pedido, cliente, endereço e lançamentos); só o seu usuário continua no banco

---

## 3. Pendências

### 3.1 Para ativar pagamento e frete reais

- [ ] Criar a aplicação no painel de desenvolvedores do Mercado Pago e copiar o **Access Token**
- [ ] Preencher `mercadopago.accessToken` no `.env` (começar com as credenciais de **teste**)
- [ ] Cadastrar o webhook no Mercado Pago apontando para `https://SEU-DOMINIO/notificacoes/pagamento` e preencher `mercadopago.webhookSecret`
- [ ] Criar conta no Melhor Envio, gerar o token e preencher `melhorenvio.token`, `melhorenvio.cepOrigem` e `melhorenvio.email`
- [ ] Trocar `melhorenvio.sandbox` para `false` ao usar o ambiente de produção
- [ ] ⚠️ **Testar o fluxo real**: as chamadas ao Mercado Pago e ao Melhor Envio não foram testadas por falta de chaves
  - O retorno automático e o webhook do Mercado Pago só funcionam com endereço público em `https`; localmente, só o fluxo manual pode ser testado
- [ ] Revisar o peso e as medidas de cada produto cadastrado (os valores padrão são 0,8 kg e 45 × 30 × 15 cm)

### 3.2 Código que precisa de atenção

- [ ] ⚠️ `app/Views/errors/html/error_exception.php` é de uma versão antiga do CodeIgniter e chama um método que não existe mais: quando acontece um erro em desenvolvimento, a própria página de erro quebra e esconde a causa real
- [ ] `RelatorioController` aponta para uma view que não existe e não tem rota nem item de menu (remover ou implementar)
- [ ] Criar um filtro de autenticação para o grupo de rotas `admin` (hoje cada método verifica `session()->isLoggedId`; um filtro é mais seguro e evita esquecer a verificação em métodos novos)
- [ ] Telas "Clientes", "Vendedores" e "Devoluções" do painel estão como "em construção"
- [ ] O cadastro de usuários (`/auth/registro`) está aberto: qualquer pessoa pode criar um acesso ao painel; restringir antes do deploy
- [ ] O cliente não tem conta na loja: só consegue ver o pedido na mesma sessão em que comprou (avaliar acompanhamento por e-mail ou login)
- [ ] O estoque é reservado só quando o pagamento é confirmado: se dois clientes comprarem a última unidade ao mesmo tempo, o estoque pode ficar negativo (avaliar reserva no momento do pedido)
- [ ] Tela de "Usuários" no painel (gerenciar acessos)
- [ ] Testes automatizados (PHPUnit) para `PedidoLibrary`, carrinho e checkout

### 3.3 Funcionalidades previstas no DOC.md ainda não feitas

- [ ] **Etiquetas do Melhor Envio**: gerar a etiqueta de envio pelo painel (hoje só existe a cotação)
- [ ] Desconto no produto (campo opcional citado no DOC.md)
- [ ] Cupom de desconto
- [ ] Notificação por e-mail das mudanças de status do pedido
- [ ] Google Analytics e Meta Pixel
- [ ] SEO básico: `title` e `meta description` por produto, `sitemap.xml`, URLs amigáveis (slug em vez do id)
- [ ] API REST pública (para um app no futuro)

### 3.4 Deploy

- [ ] Contratar hospedagem com PHP 8.1+ e MySQL (Hostinger, Umbler ou KingHost, conforme o DOC.md) e registrar o domínio `.com.br`
- [ ] Apontar o domínio para a pasta `public/`
- [ ] Ativar o SSL e forçar HTTPS (`app.forceGlobalSecureRequests = true`)
- [ ] Configurar o `.env` de produção:
  - `CI_ENVIRONMENT = production`
  - `app.baseURL` com o domínio em `https`
  - dados do banco de produção
  - chaves do Mercado Pago e do Melhor Envio de produção
- [ ] Rodar as migrations no servidor (`php spark migrate`)
- [ ] Dar permissão de escrita em `writable/` e `public/uploads/produtos/`
- [ ] Backup automático diário do banco de dados
- [ ] Testar uma compra real de valor baixo de ponta a ponta antes de divulgar

### 3.5 Burocracia (fora do código)

- [ ] Abrir o MEI (CNAE sugerido no DOC.md: 4781-4/00)
- [ ] Abrir conta bancária PJ e vincular ao Mercado Pago
- [ ] Definir a emissão de nota fiscal (prefeitura ou sistema integrado)
- [ ] Publicar na loja as políticas de troca, devolução e privacidade

---

## 4. Arquivos principais

| Arquivo | Função |
| --- | --- |
| `app/Libraries/PedidoLibrary.php` | Ciclo do pedido: criação, pagamento, envio e cancelamento |
| `app/Libraries/CarrinhoLibrary.php` | Carrinho na sessão |
| `app/Libraries/MercadoPagoLibrary.php` | Checkout Pro, consulta de pagamento e assinatura do webhook |
| `app/Libraries/MelhorEnvioLibrary.php` | Cotação de frete |
| `app/Controllers/Client/CheckoutController.php` | Checkout, cotação de frete e retorno do pagamento |
| `app/Controllers/NotificacaoController.php` | Webhook do Mercado Pago |
| `app/Controllers/Admin/PedidoController.php` | Pedidos no painel |
| `app/Controllers/Admin/FinanceiroController.php` | Lançamentos e fluxo de caixa |
| `app/Config/MercadoPago.php` e `app/Config/MelhorEnvio.php` | Configurações lidas do `.env` |
| `app/Helpers/loja_helper.php` | Formatação de moeda, datas, CEP e imagens |
| `public/assets/loja/css/loja.css` | Visual da loja |
| `public/app/` | JavaScript das telas (modules, controllers, services) |
