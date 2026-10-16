<?php echo $this->extend('Admin/layout') ?>
<?php echo $this->section('content') ?>
<section class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <form method="get" class="form-row align-items-center">
                    <div class="col-12 col-md-5 mb-2 mb-md-0">
                        <div class="input-group">
                            <input type="search" name="busca" class="form-control" placeholder="Buscar por nome ou SKU..." value="<?php echo esc($busca, 'attr') ?>">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                    <div class="col-8 col-md-3">
                        <select name="categoria" class="form-control" data-envio-automatico>
                            <option value="">Todas as categorias</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria->id ?>" <?php echo $categoriaId == $categoria->id ? 'selected' : '' ?>><?php echo esc($categoria->nome) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-4 col-md-4 text-right">
                        <a href="<?php echo base_url(route_to('produto_create')) ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i><span class="d-none d-sm-inline ml-1">Novo Produto</span>
                        </a>
                    </div>
                </form>
            </div>
            <!-- /.card-header -->
            <div class="card-body p-0">
                <?php if ($produtos === []): ?>
                    <div class="text-center text-muted py-5">
                        <i class="fas fa-box-open fa-3x mb-3"></i>
                        <p class="mb-0">Nenhum produto encontrado.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="tabela-produtos">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th class="d-none d-md-table-cell">Categoria</th>
                                    <th class="text-right">Preço</th>
                                    <th class="text-center">Estoque</th>
                                    <th class="text-center d-none d-sm-table-cell">Ativo</th>
                                    <th class="text-right">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($produtos as $produto): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo produto_imagem($produto->imagem) ?>" alt="" class="miniatura-admin mr-3">
                                                <div>
                                                    <div class="font-weight-bold"><?php echo esc($produto->nome) ?></div>
                                                    <small class="text-muted">SKU <?php echo esc($produto->sku) ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="d-none d-md-table-cell"><?php echo esc($produto->categoria ?? '-') ?></td>
                                        <td class="text-right text-nowrap"><?php echo moeda($produto->preco) ?></td>
                                        <td class="text-center">
                                            <?php if ($produto->esgotado()): ?>
                                                <span class="badge badge-danger badge-status">Esgotado</span>
                                            <?php elseif ($produto->estoqueBaixo()): ?>
                                                <span class="badge badge-warning badge-status"><?php echo $produto->estoque ?> un.</span>
                                            <?php else: ?>
                                                <span class="badge badge-light badge-status"><?php echo $produto->estoque ?> un.</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center d-none d-sm-table-cell">
                                            <?php if ($produto->ativo): ?>
                                                <span class="badge bg-success">Sim</span>
                                            <?php else: ?>
                                                <span class="badge bg-danger">Não</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="acoes-tabela">
                                                <a href="<?php echo base_url(route_to('produto_edit', $produto->id)) ?>" class="btn btn-sm btn-info" title="Editar"><i class="fas fa-pen"></i></a>
                                                <button type="button" class="btn btn-sm btn-danger" title="Excluir" data-acao="excluir" data-id="<?php echo $produto->id ?>" data-nome="<?php echo esc($produto->nome, 'attr') ?>"><i class="fas fa-trash"></i></button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
            <!-- /.card-body -->
            <div class="card-footer clearfix">
                <?php echo $pager->links('default', 'bootstrap'); ?>
            </div>
        </div>
    </div>
</section>
<?php echo $this->endSection() ?>

<?php echo $this->section('scripts') ?>
<script type="module" src="<?php echo base_url('app/modules/produtoModule.js') ?>"></script>
<?php echo $this->endSection() ?>
