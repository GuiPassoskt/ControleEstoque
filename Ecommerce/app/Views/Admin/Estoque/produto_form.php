<?php echo $this->extend('Admin/layout') ?>

<?php echo $this->section('content') ?>
<?php
/** @var \App\Entities\Produto|null $produto */
$editando = $produto !== null;
$valor = static fn (string $campo, $padrao = '') => old($campo, $editando ? $produto->{$campo} : $padrao, false);
$ativo = old('ativo', $editando ? (int) $produto->ativo : 1, false);
// O cast float da entidade descarta zeros à direita (189.90 vira 189.9)
$preco = old('preco', $editando ? number_format($produto->preco, 2, '.', '') : '', false);
?>
<section class="content">
  <div class="container-fluid">
    <form action="<?php echo $editando ? base_url(route_to('produto_update', $produto->id)) : base_url(route_to('produto_store')) ?>" method="post" enctype="multipart/form-data" id="form-produto">
      <?php echo csrf_field() ?>
      <?php if ($editando): ?>
        <input type="hidden" name="_method" value="PUT">
      <?php endif; ?>

      <div class="row">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-tag mr-2"></i>Informações do produto</h3>
            </div>
            <div class="card-body">
              <div class="form-group">
                <label for="nome">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo esc($valor('nome'), 'attr') ?>" placeholder="Ex.: Mochila Urban Explorer 25L" required maxlength="255">
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="sku">SKU</label>
                  <input type="text" class="form-control text-uppercase" id="sku" name="sku" value="<?php echo esc($valor('sku'), 'attr') ?>" placeholder="MOC-URB-25" required maxlength="50">
                </div>
                <div class="form-group col-md-6">
                  <label for="categoria_id">Categoria</label>
                  <select class="form-control" id="categoria_id" name="categoria_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($categorias as $categoria): ?>
                      <option value="<?php echo $categoria->id ?>" <?php echo $valor('categoria_id') == $categoria->id ? 'selected' : '' ?>><?php echo esc($categoria->nome) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>
              <div class="form-row">
                <div class="form-group col-md-6">
                  <label for="preco">Preço de venda</label>
                  <div class="input-group">
                    <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                    <input type="number" class="form-control" id="preco" name="preco" value="<?php echo esc($preco, 'attr') ?>" min="0.01" step="0.01" placeholder="0,00" required>
                  </div>
                </div>
                <div class="form-group col-md-6">
                  <label for="estoque">Estoque disponível</label>
                  <div class="input-group">
                    <input type="number" class="form-control" id="estoque" name="estoque" value="<?php echo esc($valor('estoque', 0), 'attr') ?>" min="0" step="1" required>
                    <div class="input-group-append"><span class="input-group-text">un.</span></div>
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="descricao">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="6" placeholder="Materiais, compartimentos, capacidade, diferenciais..."><?php echo esc($valor('descricao')) ?></textarea>
              </div>
              <div class="custom-control custom-switch">
                <input type="hidden" name="ativo" value="0">
                <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1" <?php echo $ativo ? 'checked' : '' ?>>
                <label class="custom-control-label" for="ativo">Exibir na loja</label>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="far fa-image mr-2"></i>Foto</h3>
            </div>
            <div class="card-body">
              <label for="imagem" class="preview-imagem mb-2" id="preview-imagem" title="Clique para escolher a foto">
                <?php if ($editando && $produto->imagem): ?>
                  <img src="<?php echo produto_imagem($produto->imagem) ?>" alt="">
                <?php else: ?>
                  <span class="text-muted text-center p-3">
                    <i class="fas fa-cloud-upload-alt fa-2x d-block mb-2"></i>
                    Clique para enviar uma foto
                  </span>
                <?php endif; ?>
              </label>
              <input type="file" class="d-none" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp">
              <small class="text-muted">JPG, PNG ou WEBP até 2 MB. Fotos quadradas ficam melhores na vitrine.</small>
            </div>
          </div>

          <div class="card">
            <div class="card-header">
              <h3 class="card-title"><i class="fas fa-truck mr-2"></i>Embalagem para frete</h3>
            </div>
            <div class="card-body">
              <div class="form-row">
                <div class="form-group col-6">
                  <label for="peso">Peso (kg)</label>
                  <input type="number" class="form-control" id="peso" name="peso" value="<?php echo esc($valor('peso', '0.8'), 'attr') ?>" min="0.001" step="0.001" required>
                </div>
                <div class="form-group col-6">
                  <label for="altura">Altura (cm)</label>
                  <input type="number" class="form-control" id="altura" name="altura" value="<?php echo esc($valor('altura', 45), 'attr') ?>" min="1" step="1" required>
                </div>
                <div class="form-group col-6 mb-0">
                  <label for="largura">Largura (cm)</label>
                  <input type="number" class="form-control" id="largura" name="largura" value="<?php echo esc($valor('largura', 30), 'attr') ?>" min="1" step="1" required>
                </div>
                <div class="form-group col-6 mb-0">
                  <label for="comprimento">Comprimento (cm)</label>
                  <input type="number" class="form-control" id="comprimento" name="comprimento" value="<?php echo esc($valor('comprimento', 15), 'attr') ?>" min="1" step="1" required>
                </div>
              </div>
              <small class="text-muted d-block mt-2">Usado na cotação do Melhor Envio.</small>
            </div>
          </div>
        </div>
      </div>

      <div class="d-flex justify-content-end mb-4">
        <a href="<?php echo base_url(route_to('produtos')) ?>" class="btn btn-secondary mr-2">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save mr-1"></i> Salvar produto</button>
      </div>
    </form>
  </div>
</section>
<?php echo $this->endSection() ?>

<?php echo $this->section('scripts') ?>
<script type="module" src="<?php echo base_url('app/modules/produtoModule.js') ?>"></script>
<?php echo $this->endSection() ?>
