<div class="module-wrapper indi-report-module">
    <div id="food-general">
        <h3>Izveštaj o hrani: <b><?=$general['name']?></b></h3>
        <div>Iskorišćenost: <b><?=$general['utilization']?> %</b></div>
        <div>Težina: <b><?=$general['weight']?> g</b></div>
        <div>Cena: <b><?=$general['total_price']?> rsd</b></div>
    </div>

    <div id="exTab">
        <ul class="nav nav-tabs">
            <li class="active">
                <a href="#1" data-toggle="tab">Tabela</a>
            </li>
            <li>
                <a href="#3" data-toggle="tab">Grafikon</a>
            </li>
            <li>
                <a href="#3" data-toggle="tab">Grafikon +</a>
            </li>
        </ul>
        <div class="tab-content ">
            <div class="tab-pane active" id="1">
                <table class="table table-bordered table-hover table-sm" id="nutrients-table">
                    <thead>
                        <tr>
                            <th>Nutrijent</th>
                            <th>Jedinica</th>
                            <th>Vrednost</th>
                            <th title="Preporučene dnevne doze">PDD*</th>
                        </tr>
                    </thead>
                <?php
                $lastGroup = '';
                foreach($nutrients as $nutrient) {
                    if ($lastGroup != $nutrient['group']) { ?>
                        <tr>
                            <td colspan="4" class="group-label"><?=$nutrient['group']?></td>
                        </tr>
                    <?php
                        $lastGroup = $nutrient['group'];
                    }
                    ?>
                        <tr class="<?=($nutrient['list_type'] == 'b') ? 'n_basic' : 'n_full'?>">
                            <td><?=$nutrient['name']?></td>
                            <td><?=$nutrient['unit']?></td>
                            <td>
                                <span class="left"><?=$nutrient['value']?></span>
                                <span class="right">(<?=$nutrient['percentage']?>%)</span>
                            </td>
                            <td><?=$nutrient['rdi']?></td>
                        </tr>
                <?php } ?>
                </table>
            </div>
            <div class="tab-pane" id="2">
                <h3>Grafikon osnovnih nutrijenata</h3>
            </div>
            <div class="tab-pane" id="3">
                <h3>Grafikon svih nutrijenata</h3>
            </div>
        </div>
    </div>
</div>