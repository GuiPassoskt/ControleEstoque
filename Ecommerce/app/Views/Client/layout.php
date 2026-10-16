<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Loja') ?> | Utopia Mochilas</title>
    <meta name="description" content="<?= esc($descricao ?? 'Mochilas, lancheiras e acessórios com entrega para todo o Brasil. Pague com Pix ou cartão.') ?>">
    <meta name="theme-color" content="#111111">
    <meta name="base-url" content="<?= base_url() ?>">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <meta name="csrf-header" content="<?= csrf_header() ?>">
    <link rel="icon" href="<?= base_url('assets/images/logo-utopia.jpeg') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Space+Grotesk:wght@500;700&family=Space+Mono:wght@700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <?= link_tag('assets/plugins/fontawesome-free/css/all.min.css') ?>
    <?= link_tag('assets/plugins/sweetalert2/sweetalert2.min.css') ?>
    <?= link_tag('assets/loja/css/loja.css') ?>
    <?= $this->renderSection('styles') ?>
</head>

<body class="<?= $this->renderSection('classe_body') ?>">
    <div class="barra-anuncio text-center py-2 px-3">
        <i class="fas fa-bolt me-1"></i> Pague com <strong>Pix</strong> e receba a confirmação na hora · Parcele em até <strong>12x</strong> no cartão
    </div>

    <header class="cabecalho">
        <div class="container">
            <div class="d-flex align-items-center gap-3 py-2">
                <a href="<?= base_url(route_to('loja')) ?>" class="marca me-lg-4">
                    <img src="<?= base_url('assets/images/logo-utopia.jpeg') ?>" alt="">
                    <span class="d-none d-sm-inline">Utopia</span>
                </a>

                <form action="<?= base_url(route_to('loja')) ?>" method="get" class="busca flex-grow-1 d-none d-md-block" role="search">
                    <i class="fas fa-search"></i>
                    <input type="search" name="busca" class="form-control" placeholder="Buscar mochilas, lancheiras, estojos..." value="<?= esc($busca ?? '') ?>" aria-label="Buscar produtos">
                </form>

                <div class="ms-auto d-flex align-items-center gap-2">
                    <button class="icone-carrinho border-0 d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#busca-movel" aria-label="Abrir busca">
                        <i class="fas fa-search"></i>
                    </button>
                    <a href="<?= base_url(route_to('carrinho')) ?>" class="icone-carrinho" aria-label="Carrinho com <?= (int) ($carrinhoQuantidade ?? 0) ?> itens">
                        <i class="fas fa-shopping-bag"></i>
                        <?php if (! empty($carrinhoQuantidade)): ?>
                            <span class="contador"><?= (int) $carrinhoQuantidade ?></span>
                        <?php endif ?>
                    </a>
                </div>
            </div>

            <div class="collapse d-md-none pb-2" id="busca-movel">
                <form action="<?= base_url(route_to('loja')) ?>" method="get" class="busca" role="search">
                    <i class="fas fa-search"></i>
                    <input type="search" name="busca" class="form-control" placeholder="O que você procura?" value="<?= esc($busca ?? '') ?>" aria-label="Buscar produtos">
                </form>
            </div>

            <?php if (! empty($menuCategorias)): ?>
                <nav class="menu-categorias" aria-label="Categorias">
                    <a href="<?= base_url(route_to('loja')) ?>" class="<?= empty($categoriaId) && url_is('/') ? 'ativo' : '' ?>">Todos</a>
                    <?php foreach ($menuCategorias as $categoria): ?>
                        <a href="<?= base_url(route_to('loja')) . '?categoria=' . $categoria->id ?>" class="<?= ($categoriaId ?? null) == $categoria->id ? 'ativo' : '' ?>">
                            <?= esc($categoria->nome) ?>
                        </a>
                    <?php endforeach ?>
                </nav>
            <?php endif ?>
        </div>
    </header>

    <main class="<?= $this->renderSection('classe_main') ?>">
        <?= $this->renderSection('content') ?>
    </main>

    <footer class="rodape">
        <div class="container py-5">
            <div class="row g-4">
                <div class="col-lg-4">
                    <a href="<?= base_url(route_to('loja')) ?>" class="marca mb-3">
                        <img src="<?= base_url('assets/images/logo-utopia.jpeg') ?>" alt="">
                        <span>Utopia</span>
                    </a>
                    <p class="mb-0" style="max-width: 22rem;">Mochilas pensadas para a rotina: escola, trabalho e aventura. Qualidade, conforto e preço justo.</p>
                </div>
                <div class="col-6 col-lg-2">
                    <h6>Loja</h6>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        <li><a href="<?= base_url(route_to('loja')) ?>">Todos os produtos</a></li>
                        <?php foreach (array_slice($menuCategorias ?? [], 0, 3) as $categoria): ?>
                            <li><a href="<?= base_url(route_to('loja')) . '?categoria=' . $categoria->id ?>"><?= esc($categoria->nome) ?></a></li>
                        <?php endforeach ?>
                    </ul>
                </div>
                <div class="col-6 col-lg-3">
                    <h6>Atendimento</h6>
                    <ul class="list-unstyled d-grid gap-2 mb-0">
                        <li class="text-break"><i class="far fa-envelope me-2"></i>mochilasutopia@gmail.com</li>
                        <li><i class="far fa-clock me-2"></i>Seg. a sex., 9h às 18h</li>
                        <li><a href="<?= base_url(route_to('carrinho')) ?>"><i class="fas fa-shopping-bag me-2"></i>Meu carrinho</a></li>
                    </ul>
                </div>
                <div class="col-lg-3">
                    <h6>Pagamento seguro</h6>
                    <div class="d-flex flex-wrap gap-2 mb-3">
                        <span class="selo-pagamento"><i class="fas fa-qrcode"></i> Pix</span>
                        <span class="selo-pagamento"><i class="far fa-credit-card"></i> Cartão</span>
                        <span class="selo-pagamento"><i class="fas fa-handshake"></i> Mercado Pago</span>
                    </div>
                    <span class="selo-pagamento"><i class="fas fa-lock"></i> Site protegido</span>
                </div>
            </div>
        </div>
        <div class="rodape-base">
            <div class="container py-3 d-flex flex-column flex-md-row justify-content-between gap-2">
                <span>&copy; <?= date('Y') ?> Utopia Mochilas. Todos os direitos reservados.</span>
                <span>Desenvolvido por DecodeZero</span>
            </div>
        </div>
    </footer>

    <div id="mensagens-sessao" hidden
        data-success="<?= esc(session()->getFlashdata('success') ?? '', 'attr') ?>"
        data-error="<?= esc(session()->getFlashdata('error') ?? '', 'attr') ?>"></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= script_tag('assets/plugins/sweetalert2/sweetalert2.all.min.js') ?>
    <script type="module" src="<?= base_url('app/modules/lojaModule.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>

</html>
