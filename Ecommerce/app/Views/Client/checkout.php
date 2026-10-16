<?= $this->extend('Client/layout') ?>

<?= $this->section('content') ?>
<?php
$erros = session('errors') ?? [];
$invalido = static fn (string $campo) => isset($erros[$campo]) ? 'is-invalid' : '';
$erro = static fn (string $campo) => isset($erros[$campo])
    ? '<div class="invalid-feedback">' . esc($erros[$campo]) . '</div>'
    : '';
$pagamentoEscolhido = old('pagamento', 'pix', false);
?>
<div class="container py-4 py-lg-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h2 mb-0">Finalizar compra</h1>
        <span class="small text-cinza d-none d-sm-inline"><i class="fas fa-lock me-1"></i>Ambiente seguro</span>
    </div>

    <form action="<?= base_url(route_to('checkout_finalizar')) ?>" method="post" id="form-checkout" novalidate
        data-url-frete="<?= base_url(route_to('checkout_frete')) ?>"
        data-frete-selecionado="<?= esc(old('frete', '', false), 'attr') ?>"
        data-subtotal="<?= $subtotal ?>">
        <?= csrf_field() ?>

        <div class="row g-4">
            <div class="col-lg-7">
                <section class="painel">
                    <h2 class="painel-titulo"><span class="passo">1</span> Seus dados</h2>
                    <div class="row g-3">
                        <div class="col-12">
                            <label for="nome" class="form-label">Nome completo</label>
                            <input type="text" class="form-control <?= $invalido('nome') ?>" id="nome" name="nome" value="<?= esc(old('nome', '', false), 'attr') ?>" autocomplete="name" required>
                            <?= $erro('nome') ?>
                        </div>
                        <div class="col-md-7">
                            <label for="email" class="form-label">E-mail</label>
                            <input type="email" class="form-control <?= $invalido('email') ?>" id="email" name="email" value="<?= esc(old('email', '', false), 'attr') ?>" autocomplete="email" required>
                            <?= $erro('email') ?>
                        </div>
                        <div class="col-md-5">
                            <label for="telefone" class="form-label">Celular / WhatsApp</label>
                            <input type="tel" class="form-control <?= $invalido('telefone') ?>" id="telefone" name="telefone" value="<?= esc(old('telefone', '', false), 'attr') ?>" placeholder="(00) 00000-0000" autocomplete="tel" data-mascara="telefone" required>
                            <?= $erro('telefone') ?>
                        </div>
                    </div>
                </section>

                <section class="painel">
                    <h2 class="painel-titulo"><span class="passo">2</span> Endereço de entrega</h2>
                    <div class="row g-3">
                        <div class="col-sm-5 col-md-4">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control <?= $invalido('cep') ?>" id="cep" name="cep" value="<?= esc(old('cep', '', false), 'attr') ?>" placeholder="00000-000" inputmode="numeric" autocomplete="postal-code" data-mascara="cep" required>
                            <?= $erro('cep') ?>
                        </div>
                        <div class="col-sm-7 col-md-8 d-flex align-items-end">
                            <a href="https://buscacepinter.correios.com.br/app/endereco/index.php" target="_blank" rel="noopener" class="small mb-2">Não sei meu CEP <i class="fas fa-external-link-alt ms-1"></i></a>
                        </div>
                        <div class="col-md-9">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control <?= $invalido('endereco') ?>" id="endereco" name="endereco" value="<?= esc(old('endereco', '', false), 'attr') ?>" autocomplete="address-line1" required>
                            <?= $erro('endereco') ?>
                        </div>
                        <div class="col-5 col-md-3">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control <?= $invalido('numero') ?>" id="numero" name="numero" value="<?= esc(old('numero', '', false), 'attr') ?>" inputmode="numeric" required>
                            <?= $erro('numero') ?>
                        </div>
                        <div class="col-7 col-md-5">
                            <label for="complemento" class="form-label">Complemento <span class="text-cinza fw-normal">(opcional)</span></label>
                            <input type="text" class="form-control <?= $invalido('complemento') ?>" id="complemento" name="complemento" value="<?= esc(old('complemento', '', false), 'attr') ?>" autocomplete="address-line2">
                            <?= $erro('complemento') ?>
                        </div>
                        <div class="col-md-7">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control <?= $invalido('bairro') ?>" id="bairro" name="bairro" value="<?= esc(old('bairro', '', false), 'attr') ?>" required>
                            <?= $erro('bairro') ?>
                        </div>
                        <div class="col-8 col-md-9">
                            <label for="cidade" class="form-label">Cidade</label>
                            <input type="text" class="form-control <?= $invalido('cidade') ?>" id="cidade" name="cidade" value="<?= esc(old('cidade', '', false), 'attr') ?>" autocomplete="address-level2" required>
                            <?= $erro('cidade') ?>
                        </div>
                        <div class="col-4 col-md-3">
                            <label for="estado" class="form-label">UF</label>
                            <input type="text" class="form-control text-uppercase <?= $invalido('estado') ?>" id="estado" name="estado" value="<?= esc(old('estado', '', false), 'attr') ?>" maxlength="2" autocomplete="address-level1" required>
                            <?= $erro('estado') ?>
                        </div>
                    </div>
                </section>

                <section class="painel">
                    <h2 class="painel-titulo"><span class="passo">3</span> Entrega</h2>
                    <div id="opcoes-frete" aria-live="polite">
                        <p class="text-cinza mb-0"><i class="fas fa-map-marker-alt me-2"></i>Informe o CEP para ver as opções de entrega.</p>
                    </div>
                    <?php if (isset($erros['frete'])): ?>
                        <div class="text-danger small mt-2"><?= esc($erros['frete']) ?></div>
                    <?php endif ?>
                </section>

                <section class="painel">
                    <h2 class="painel-titulo"><span class="passo">4</span> Pagamento</h2>
                    <div class="grade-pagamento">
                        <label class="opcao">
                            <input class="form-check-input" type="radio" name="pagamento" value="pix" <?= $pagamentoEscolhido === 'pix' ? 'checked' : '' ?>>
                            <span class="icone-opcao"><i class="fas fa-qrcode"></i></span>
                            <span>
                                <strong class="d-block">Pix</strong>
                                <span class="small text-cinza">Aprovação na hora</span>
                            </span>
                        </label>
                        <label class="opcao">
                            <input class="form-check-input" type="radio" name="pagamento" value="cartao" <?= $pagamentoEscolhido === 'cartao' ? 'checked' : '' ?>>
                            <span class="icone-opcao"><i class="far fa-credit-card"></i></span>
                            <span>
                                <strong class="d-block">Cartão</strong>
                                <span class="small text-cinza">Crédito em até 12x ou débito</span>
                            </span>
                        </label>
                    </div>
                    <?php if (isset($erros['pagamento'])): ?>
                        <div class="text-danger small mt-2"><?= esc($erros['pagamento']) ?></div>
                    <?php endif ?>
                    <p class="small text-cinza mt-3 mb-0">
                        <i class="fas fa-shield-alt me-1"></i>
                        <?php if ($pagamentoOnline): ?>
                            Você será levado ao ambiente seguro do Mercado Pago para concluir o pagamento. Não armazenamos dados do seu cartão.
                        <?php else: ?>
                            Após confirmar, entraremos em contato para concluir o pagamento.
                        <?php endif ?>
                    </p>
                </section>
            </div>

            <div class="col-lg-5">
                <aside class="painel lateral-fixa">
                    <h2 class="h5 mb-3">Resumo do pedido</h2>
                    <div class="d-grid gap-3 mb-3">
                        <?php foreach ($itens as $item): ?>
                            <div class="d-flex align-items-center gap-3">
                                <div class="position-relative">
                                    <img src="<?= produto_imagem($item['produto']->imagem) ?>" alt="" class="miniatura miniatura-sm">
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-dark"><?= $item['quantidade'] ?></span>
                                </div>
                                <div class="flex-grow-1 small fw-semibold lh-sm"><?= esc($item['produto']->nome) ?></div>
                                <div class="small fw-semibold text-nowrap"><?= moeda($item['subtotal']) ?></div>
                            </div>
                        <?php endforeach ?>
                    </div>
                    <div class="resumo-linha"><span>Subtotal</span><span><?= moeda($subtotal) ?></span></div>
                    <div class="resumo-linha"><span>Frete</span><span id="resumo-frete" class="text-cinza">—</span></div>
                    <div class="resumo-total">
                        <span class="fw-semibold">Total</span>
                        <span class="valor" id="resumo-total"><?= moeda($subtotal) ?></span>
                    </div>
                    <button type="submit" class="btn btn-acento btn-lg w-100 mt-4" id="botao-finalizar">
                        <?= $pagamentoOnline ? 'Ir para o pagamento' : 'Confirmar pedido' ?> <i class="fas fa-arrow-right ms-2"></i>
                    </button>
                    <p class="small text-cinza text-center mt-3 mb-0">Ao confirmar, você concorda com nossas condições de venda e troca.</p>
                </aside>
            </div>
        </div>
    </form>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script type="module" src="<?= base_url('app/modules/checkoutModule.js') ?>"></script>
<?= $this->endSection() ?>
