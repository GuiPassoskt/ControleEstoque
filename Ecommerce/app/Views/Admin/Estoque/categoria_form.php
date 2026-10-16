<?php echo $this->extend('Admin/layout') ?>

<?php echo $this->section('styles') ?>
<?php echo $this->endSection() ?>

<?php echo $this->section('content') ?>
<?php $editando = isset($categoria->id); ?>
<div class="container-fluid">
  <div class="card" style="max-width: 720px;">
    <div class="card-header">
      <h3 class="card-title"><?php echo $editando ? 'Editar Categoria' : 'Cadastro de Categoria' ?></h3>
    </div>

    <form action="<?php echo $editando ? base_url(route_to('categoria_update', $categoria->id)) : base_url(route_to('categoria_store')) ?>" method="post">
      <?php echo csrf_field() ?>
      <?php if ($editando): ?>
        <input type="hidden" name="_method" value="PUT">
      <?php endif; ?>
      <div class="card-body">
        <div class="row">
          <div class="col-md-12">
            <div class="form-group">
              <label for="nome">Nome</label>
              <input type="text" class="form-control" id="nome" name="nome" placeholder="Nome da categoria" value="<?php echo esc(old('nome', $categoria->nome ?? '', false), 'attr') ?>" required>
            </div>
          </div>

          <div class="col-md-12">
            <div class="form-group">
              <label for="ativo">Status</label>
              <select class="form-control" id="ativo" name="ativo" required>
                <option value="1" <?= (isset($categoria->ativo) && $categoria->ativo == 1) ? 'selected' : '' ?>>Ativo</option>
                <option value="0" <?= (isset($categoria->ativo) && $categoria->ativo == 0) ? 'selected' : '' ?>>Inativo</option>
              </select>
            </div>
          </div>
        </div>
      </div>

      <div class="card-footer">
        <button type="submit" class="btn btn-primary">Salvar</button>
        <a href="<?php echo base_url(route_to('categorias')) ?>" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>

<?php echo $this->endSection() ?>
