<?php echo $this->extend('Admin/layout') ?>
<?php echo $this->section('content') ?>
<?php $tipo = old('tipo', 'Saída', false); ?>
<section class="content">
    <div class="container-fluid">
        <div class="card" style="max-width: 720px;">
            <div class="card-header">
                <h3 class="card-title">Novo lançamento manual</h3>
            </div>
            <form action="<?php echo base_url(route_to('financeiro_store')) ?>" method="post">
                <?php echo csrf_field() ?>
                <div class="card-body">
                    <div class="form-group">
                        <label>Tipo</label>
                        <div class="seletor-tipo">
                            <div>
                                <input type="radio" id="tipo-saida" name="tipo" value="Saída" <?php echo $tipo === 'Saída' ? 'checked' : '' ?>>
                                <label for="tipo-saida" class="saida"><i class="fas fa-arrow-up"></i> Saída <small class="font-weight-normal text-muted d-none d-sm-inline">(despesa, compra de estoque)</small></label>
                            </div>
                            <div>
                                <input type="radio" id="tipo-entrada" name="tipo" value="Entrada" <?php echo $tipo === 'Entrada' ? 'checked' : '' ?>>
                                <label for="tipo-entrada" class="entrada"><i class="fas fa-arrow-down"></i> Entrada <small class="font-weight-normal text-muted d-none d-sm-inline">(aporte, venda avulsa)</small></label>
                            </div>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="valor">Valor</label>
                            <div class="input-group">
                                <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                <input type="number" class="form-control" id="valor" name="valor" min="0.01" step="0.01" value="<?php echo esc(old('valor', '', false), 'attr') ?>" required>
                            </div>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="data_movimento">Data</label>
                            <input type="datetime-local" class="form-control" id="data_movimento" name="data_movimento" value="<?php echo esc(old('data_movimento', date('Y-m-d\TH:i'), false), 'attr') ?>" required>
                        </div>
                    </div>
                    <div class="form-group mb-0">
                        <label for="descricao">Descrição</label>
                        <input type="text" class="form-control" id="descricao" name="descricao" maxlength="255" placeholder="Ex.: Compra de 20 mochilas do fornecedor" value="<?php echo esc(old('descricao', '', false), 'attr') ?>" required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save mr-1"></i>Registrar</button>
                    <a href="<?php echo base_url(route_to('financeiro')) ?>" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</section>
<?php echo $this->endSection() ?>
