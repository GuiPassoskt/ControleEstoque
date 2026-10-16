<?= $this->extend('Client/layout') ?>

<?= $this->section('content') ?>
<div class="container py-4 py-lg-5">
    <div class="d-flex align-items-end justify-content-between mb-4">
        <div>
            <h1 class="h2 mb-1">Seu carrinho</h1>
            <span class="text-cinza"><?= $carrinhoQuantidade ?> <?= $carrinhoQuantidade === 1 ? 'item' : 'itens' ?></span>
        </div>
        <a href="<?= base_url(route_to('loja')) ?>" class="small fw-semibold d-none d-sm-inline"><i class="fas fa-arrow-left me-1"></i> Continuar comprando</a>
    </div>

    <?php if ($itens === []): ?>
        <div class="estado-vazio">
            <div class="icone"><i class="fas fa-shopping-bag"></i></div>
            <h2 class="h5">Seu carrinho está vazio</h2>
            <p class="text-cinza mb-4">Que tal dar uma olhada nas novidades?</p>
            <a href="<?= base_url(route_to('loja')) ?>" class="btn btn-acento btn-lg">Explorar produtos</a>
        </div>
    <?php else: ?>
        <div class="row g-4">
            <div class="col-lg-8">
                <form action="<?= base_url(route_to('carrinho_atualizar')) ?>" method="post" class="painel" id="form-carrinho" data-envio-automatico>
                    <?= csrf_field() ?>
                    <!-- Botão padrão do Enter: sem ele o Enter acionaria o primeiro "Remover" -->
                    <button type="submit" class="visually-hidden" tabindex="-1">Atualizar quantidades</button>
                    <?php foreach ($itens as $item): ?>
                        <?php $produto = $item['produto']; ?>
                        <div class="item-carrinho">
                            <a href="<?= base_url(route_to('loja_produto', $produto->id)) ?>">
                                <img src="<?= produto_imagem($produto->imagem) ?>" alt="<?= esc($produto->nome, 'attr') ?>">
                            </a>
                            <div>
                                <div class="d-flex justify-content-between gap-2">
                                    <div>
                                        <a href="<?= base_url(route_to('loja_produto', $produto->id)) ?>" class="fw-semibold"><?= esc($produto->nome) ?></a>
                                        <div class="small text-cinza"><?= moeda($produto->preco) ?> / unidade</div>
                                        <?php if ($item['quantidade'] >= $produto->estoque): ?>
                                            <div class="small text-acento">Quantidade máxima em estoque</div>
                                        <?php endif ?>
                                    </div>
                                    <div class="preco fs-5 text-nowrap"><?= moeda($item['subtotal']) ?></div>
                                </div>
                                <div class="detalhes mt-3">
                                    <div class="quantidade" data-quantidade>
                                        <button type="button" data-quantidade-menos aria-label="Diminuir"><i class="fas fa-minus"></i></button>
                                        <input type="number" name="quantidades[<?= $produto->id ?>]" value="<?= $item['quantidade'] ?>" min="0" max="<?= $produto->estoque ?>" aria-label="Quantidade de <?= esc($produto->nome, 'attr') ?>">
                                        <button type="button" data-quantidade-mais aria-label="Aumentar"><i class="fas fa-plus"></i></button>
                                    </div>
                                    <button type="submit" class="btn-remover" formaction="<?= base_url(route_to('carrinho_remover', $produto->id)) ?>">
                                        <i class="far fa-trash-alt me-1"></i> Remover
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach ?>
                </form>
            </div>

            <div class="col-lg-4">
                <aside class="painel lateral-fixa">
                    <h2 class="h5 mb-3">Resumo</h2>
                    <div class="resumo-linha"><span>Subtotal</span><strong><?= moeda($subtotal) ?></strong></div>
                    <div class="resumo-linha"><span>Frete</span><span class="text-cinza small">Calculado no próximo passo</span></div>
                    <div class="resumo-total">
                        <span class="fw-semibold">Total</span>
                        <span class="valor"><?= moeda($subtotal) ?></span>
                    </div>
                    <div class="small text-cinza text-end mb-4">ou até 12x de <?= moeda($subtotal / 12) ?></div>
                    <a href="<?= base_url(route_to('checkout')) ?>" class="btn btn-acento btn-lg w-100">
                        Finalizar compra <i class="fas fa-arrow-right ms-2"></i>
                    </a>
                    <a href="<?= base_url(route_to('loja')) ?>" class="btn btn-link w-100 mt-2 text-dark text-decoration-none">Continuar comprando</a>
                    <div class="d-flex justify-content-center gap-3 mt-3 small text-cinza">
                        <span><i class="fas fa-lock me-1"></i>Compra segura</span>
                        <span><i class="fas fa-qrcode me-1"></i>Pix</span>
                        <span><i class="far fa-credit-card me-1"></i>Cartão</span>
                    </div>
                </aside>
            </div>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
