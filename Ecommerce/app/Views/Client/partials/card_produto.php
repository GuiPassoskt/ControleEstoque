<?php
/**
 * @var \App\Entities\Produto $produto
 */
$link = base_url(route_to('loja_produto', $produto->id));
?>
<article class="card-produto <?= $produto->esgotado() ? 'esgotado' : '' ?>">
    <a href="<?= $link ?>" class="foto">
        <img src="<?= produto_imagem($produto->imagem) ?>" alt="<?= esc($produto->nome, 'attr') ?>" loading="lazy">
        <?php if ($produto->esgotado()): ?>
            <span class="etiqueta etiqueta-esgotado">Esgotado</span>
        <?php elseif ($produto->estoqueBaixo()): ?>
            <span class="etiqueta etiqueta-ultimas">Últimas unidades</span>
        <?php endif ?>
    </a>
    <div class="corpo">
        <span class="categoria"><?= esc($produto->categoria ?? '') ?></span>
        <a href="<?= $link ?>" class="nome"><?= esc($produto->nome) ?></a>
        <div class="mt-auto">
            <div class="preco"><?= moeda($produto->preco) ?></div>
            <div class="parcelas mb-3">Pix ou até 12x no cartão</div>
            <?php if ($produto->esgotado()): ?>
                <button type="button" class="btn btn-contorno w-100" disabled>Indisponível</button>
            <?php else: ?>
                <form action="<?= base_url(route_to('carrinho_adicionar')) ?>" method="post">
                    <?= csrf_field() ?>
                    <input type="hidden" name="id_produto" value="<?= $produto->id ?>">
                    <button type="submit" class="btn btn-preto w-100">
                        <i class="fas fa-shopping-bag me-2"></i>Adicionar
                    </button>
                </form>
            <?php endif ?>
        </div>
    </div>
</article>
