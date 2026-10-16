<?= $this->extend('Client/layout') ?>

<?= $this->section('classe_main') ?>com-barra-movel<?= $this->endSection() ?>

<?= $this->section('content') ?>
<?php /** @var \App\Entities\Produto $produto */ ?>
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb small mb-0">
            <li class="breadcrumb-item"><a href="<?= base_url(route_to('loja')) ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= base_url(route_to('loja')) . '?categoria=' . $produto->categoria_id ?>"><?= esc($produto->categoria) ?></a></li>
            <li class="breadcrumb-item active text-truncate" aria-current="page"><?= esc($produto->nome) ?></li>
        </ol>
    </nav>

    <div class="row g-4 g-lg-5">
        <div class="col-lg-6">
            <div class="galeria lateral-fixa">
                <img src="<?= produto_imagem($produto->imagem) ?>" alt="<?= esc($produto->nome, 'attr') ?>">
            </div>
        </div>

        <div class="col-lg-6">
            <span class="text-uppercase small text-cinza fw-semibold" style="letter-spacing: .08em;"><?= esc($produto->categoria) ?></span>
            <h1 class="produto-titulo mt-2 mb-2"><?= esc($produto->nome) ?></h1>
            <div class="small text-cinza mb-4">SKU: <?= esc($produto->sku) ?></div>

            <div class="produto-preco"><?= moeda($produto->preco) ?></div>
            <div class="text-cinza mb-3">no Pix ou em até 12x de <?= moeda($produto->preco / 12) ?> no cartão*</div>

            <?php if ($produto->esgotado()): ?>
                <span class="disponibilidade text-danger mb-4">Produto esgotado</span>
            <?php elseif ($produto->estoqueBaixo()): ?>
                <span class="disponibilidade text-acento mb-4">Restam apenas <?= $produto->estoque ?> unidade(s)</span>
            <?php else: ?>
                <span class="disponibilidade text-success mb-4">Em estoque, pronto para envio</span>
            <?php endif ?>

            <?php if (! $produto->esgotado()): ?>
                <form action="<?= base_url(route_to('carrinho_adicionar')) ?>" method="post" class="mt-4" id="form-compra">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_produto" value="<?= $produto->id ?>">

                    <label class="form-label d-block" for="quantidade">Quantidade</label>
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
                        <div class="quantidade" data-quantidade>
                            <button type="button" data-quantidade-menos aria-label="Diminuir"><i class="fas fa-minus"></i></button>
                            <input type="number" id="quantidade" name="quantidade" value="1" min="1" max="<?= $produto->estoque ?>">
                            <button type="button" data-quantidade-mais aria-label="Aumentar"><i class="fas fa-plus"></i></button>
                        </div>
                    </div>

                    <div class="d-none d-lg-grid gap-2" style="grid-template-columns: 1fr 1fr;">
                        <button type="submit" class="btn btn-contorno btn-lg"><i class="fas fa-shopping-bag me-2"></i>Adicionar ao carrinho</button>
                        <button type="submit" name="comprar_agora" value="1" class="btn btn-acento btn-lg">Comprar agora</button>
                    </div>

                    <div class="barra-compra-movel d-lg-none">
                        <div class="d-flex align-items-center gap-2">
                            <div class="me-auto">
                                <div class="small text-cinza lh-1">Total</div>
                                <div class="preco"><?= moeda($produto->preco) ?></div>
                            </div>
                            <button type="submit" class="btn btn-contorno" aria-label="Adicionar ao carrinho"><i class="fas fa-shopping-bag"></i></button>
                            <button type="submit" name="comprar_agora" value="1" class="btn btn-acento">Comprar agora</button>
                        </div>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-light border mt-4 mb-0">
                    <i class="far fa-bell me-2"></i>Este produto voltará em breve. Confira outras opções abaixo.
                </div>
            <?php endif ?>

            <ul class="lista-garantias mt-4">
                <li><i class="fas fa-truck"></i><div><strong>Frete para todo o Brasil</strong><br><span class="text-cinza">Calculado pelo CEP na finalização da compra.</span></div></li>
                <li><i class="fas fa-qrcode"></i><div><strong>Pix com confirmação imediata</strong><br><span class="text-cinza">Seu pedido é separado assim que o pagamento é aprovado.</span></div></li>
                <li><i class="fas fa-shield-alt"></i><div><strong>Compra segura com Mercado Pago</strong><br><span class="text-cinza">Não armazenamos dados do seu cartão.</span></div></li>
            </ul>

            <?php if (! empty($produto->descricao)): ?>
                <div class="painel mt-4">
                    <h2 class="h6 mb-3">Descrição</h2>
                    <div class="text-cinza" style="white-space: pre-line;"><?= esc($produto->descricao) ?></div>
                    <hr>
                    <div class="row small text-cinza g-2">
                        <div class="col-6"><i class="fas fa-ruler-combined me-2"></i><?= $produto->altura ?> × <?= $produto->largura ?> × <?= $produto->comprimento ?> cm</div>
                        <div class="col-6"><i class="fas fa-weight-hanging me-2"></i><?= number_format($produto->peso, 2, ',', '.') ?> kg</div>
                    </div>
                </div>
            <?php endif ?>
            <p class="small text-cinza mt-3 mb-0">* Parcelamento sujeito às condições do Mercado Pago.</p>
        </div>
    </div>

    <?php if ($relacionados !== []): ?>
        <section class="mt-5 pt-lg-4">
            <h2 class="h4 mb-4">Você também pode gostar</h2>
            <div class="row row-cols-2 row-cols-lg-4 g-3 g-lg-4">
                <?php foreach ($relacionados as $relacionado): ?>
                    <div class="col"><?= view('Client/partials/card_produto', ['produto' => $relacionado]) ?></div>
                <?php endforeach ?>
            </div>
        </section>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
