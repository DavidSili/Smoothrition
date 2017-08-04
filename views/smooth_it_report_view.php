<div class="module-wrapper smooth-it-report-module">
    <div id="food-general">
        <h3>Izveštaj o smutiju od: <b><?=$general['name']?></b></h3>
        <div class="left-box">
            <div>Ukupna iskorišćenost: <b><?=$general['utilization']?> %</b></div>
            <div>Ukupna težina: <b><?=$general['weight']?> g</b></div>
            <div>Ukupna cena: <b><?=$general['total_price']?> rsd</b></div>
        </div>
        <div class="right-box">
            <button id="detailed" class="btn btn-success" data-shows="basic" data-detailed="Detaljnije" data-basic="Osnovno" ><i class="fa fa-toggle-down"></i> <span class="btn-text">Detaljnije</span></button>
        </div>
    </div>

    <div class="response-panel">
        <table class="table table-bordered table-hover table-sm" id="nutrients-table">
            <thead>
                <tr>
                    <th>Nutrijent</th>
                    <th class="values-col">Vrednost</th>
                    <th title="Preporučene dnevne doze">PDD*</th>
                </tr>
            </thead>
        <?php
        $lastGroup = '';
        foreach($nutrients as $nutrient) {
            if ($lastGroup != $nutrient['group']) { ?>
                <tr>
                    <td colspan="3" class="group-label"><?=$nutrient['group']?></td>
                </tr>
            <?php
                $lastGroup = $nutrient['group'];
            }
            ?>
                <tr class="<?=($nutrient['list_type'] == 'b') ? 'n_basic' : 'n_full'?>">
                    <td><?=$nutrient['name']?></td>
                    <td class="value-cell">
                        <div class="cell-histogram" style="width:<?=($nutrient['percentage'] >= 100) ? '140px;background-color:#cef0c0;' : round(($nutrient['percentage'] * 1.4)).'px;'?>"></div>
                        <div class="cell-content">
                            <span class="left"><?=$nutrient['value'].' '.$nutrient['unit']?></span>
                            <span class="right">(<?=$nutrient['percentage']?>%)</span>
                        </div>
                    </td>
                    <td><?=$nutrient['rdi']?></td>
                </tr>
        <?php } ?>
        </table>
    </div>
</div>