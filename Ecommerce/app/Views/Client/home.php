<?= $this->extend('Client/layout') ?>

<?= $this->section('content') ?>
<?php
$filtrando = $busca !== '' || $categoriaId !== null;
$primeiraPagina = $pager->getCurrentPage() === 1;

$tituloListagem = 'Todos os produtos';
foreach ($menuCategorias as $categoria) {
    if ($categoria->id == $categoriaId) {
        $tituloListagem = $categoria->nome;
    }
}
if ($busca !== '') {
    $tituloListagem = 'Resultados para "' . $busca . '"';
}
?>

<div class="container py-4">
    <?php if (! $filtrando && $primeiraPagina): ?>
        <section class="hero mb-4">
            <div class="row align-items-center g-4">
                <div class="col-lg-7">
                    <span class="selo mb-3"><i class="fas fa-truck"></i> Enviamos para todo o Brasil</span>
                    <h1 class="mb-3">Mochilas que acompanham a sua rotina.</h1>
                    <p class="mb-4">Da escola ao escritório, da cidade à trilha. Escolha a sua, pague com Pix ou cartão e acompanhe a entrega em tempo real.</p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="#produtos" class="btn btn-acento btn-lg">Ver coleção <i class="fas fa-arrow-down ms-2"></i></a>
                        <?php if (isset($menuCategorias[0])): ?>
                            <a href="<?= base_url(route_to('loja')) . '?categoria=' . $menuCategorias[0]->id ?>" class="btn btn-outline-light btn-lg"><?= esc($menuCategorias[0]->nome) ?></a>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-lg-5 text-center d-none d-lg-block">
                    <img src="<?= base_url('assets/loja/img/mochila.svg') ?>" alt="" class="hero-ilustracao">
                </div>
            </div>
        </section>

        <section class="beneficios mb-5" aria-label="Vantagens">
            <div class="beneficio"><i class="fas fa-truck"></i><div><strong>Frete calculado na hora</strong><span>Correios e transportadoras</span></div></div>
            <div class="beneficio"><i class="fas fa-qrcode"></i><div><strong>Pix com aprovação imediata</strong><span>Seu pedido sai mais rápido</span></div></div>
            <div class="beneficio"><i class="far fa-credit-card"></i><div><strong>Até 12x no cartão</strong><span>Pagamento via Mercado Pago</span></div></div>
            <div class="beneficio"><i class="fas fa-shield-alt"></i><div><strong>Compra 100% segura</strong><span>Seus dados protegidos</span></div></div>
        </section>
    <?php endif ?>

    <section id="produtos">
        <div class="barra-listagem mb-4">
            <div>
                <h2 class="h3 mb-1"><?= esc($tituloListagem) ?></h2>
                <span class="text-cinza small"><?= $pager->getTotal() ?> <?= $pager->getTotal() === 1 ? 'produto encontrado' : 'produtos encontrados' ?></span>
            </div>
            <form method="get" class="d-flex align-items-center gap-2" data-envio-automatico>
                <?php if ($busca !== ''): ?><input type="hidden" name="busca" value="<?= esc($busca, 'attr') ?>"><?php endif ?>
                <?php if ($categoriaId): ?><input type="hidden" name="categoria" value="<?= (int) $categoriaId ?>"><?php endif ?>
                <label for="ordem" class="small text-cinza text-nowrap">Ordenar por</label>
                <select name="ordem" id="ordem" class="form-select form-select-sm" style="min-width: 170px;">
                    <?php foreach (['recentes' => 'Novidades', 'menor_preco' => 'Menor preço', 'maior_preco' => 'Maior preço', 'nome' => 'Nome (A-Z)'] as $valor => $rotulo): ?>
                        <option value="<?= $valor ?>" <?= $ordem === $valor ? 'selected' : '' ?>><?= $rotulo ?></option>
                    <?php endforeach ?>
                </select>
            </form>
        </div>

        <?php if ($produtos === []): ?>
            <div class="estado-vazio">
                <div class="icone"><i class="fas fa-search"></i></div>
                <h3 class="h5">Nenhum produto encontrado</h3>
                <p class="text-cinza mb-4">Tente outro termo ou navegue pelas categorias.</p>
                <a href="<?= base_url(route_to('loja')) ?>" class="btn btn-preto">Ver todos os produtos</a>
            </div>
        <?php else: ?>
            <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3 g-lg-4">
                <?php foreach ($produtos as $produto): ?>
                    <div class="col"><?= view('Client/partials/card_produto', ['produto' => $produto]) ?></div>
                <?php endforeach ?>
            </div>

            <div class="mt-5">
                <?= $pager->links('default', 'bootstrap') ?>
            </div>
        <?php endif ?>
    </section>
</div>
<?= $this->endSection() ?>
