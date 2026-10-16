<?php echo $this->extend('Admin/layout') ?>
<?php echo $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header d-flex align-items-center">
                        <h3 class="card-title">Lista de Categorias</h3>
                        <a href="<?php echo base_url(route_to('categoria_create')) ?>" class="btn btn-sm btn-primary ml-auto"><i class="fas fa-plus mr-1"></i>Nova Categoria</a>
                    </div>
                    <!-- /.card-header -->
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" id="tabela-categorias">
                                <thead>
                                    <tr>
                                        <th style="width: 60px">#</th>
                                        <th>Nome</th>
                                        <th class="text-center">Ativo</th>
                                        <th class="text-right">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($categorias as $categoria): ?>
                                    <tr>
                                        <td class="text-muted"><?php echo $categoria->id; ?></td>
                                        <td class="font-weight-bold"><?php echo esc($categoria->nome); ?></td>
                                        <td class="text-center">
                                            <?php if ($categoria->ativo): ?>
                                                <span class="badge bg-success">Sim</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Não</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="acoes-tabela">
                                                <a href="<?php echo base_url(route_to('categoria_edit', $categoria->id)) ?>" class="btn btn-sm btn-info" title="Editar"><i class="fas fa-pen"></i></a>
                                                <button type="button" class="btn btn-sm btn-danger" title="Excluir" data-acao="excluir" data-id="<?php echo $categoria->id; ?>"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer clearfix">
                        <?php echo $pager->links('default', 'bootstrap'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $this->endSection() ?>

<?php echo $this->section('scripts') ?>
<script src="<?php echo base_url('app/modules/categoriaModule.js') ?>" type="module"></script>
<?php echo $this->endSection() ?>
