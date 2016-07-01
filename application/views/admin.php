<?php
/**
 * User: RN
 * Date: 6/21/2016
 * Time: 09:55
 */

    /** @var array $daftarProduk */
    /** @var array $daftarPenjualan */
    $daftarAlertProduk = array();
    foreach ($daftarProduk as $produk)
    {
        if ($produk->stok < 4)
            $daftarAlertProduk[] = $produk;
    }

    if (!empty($daftarAlertProduk)):
?>
    <div class="row">
        <div class="medium-12 columns">
        <div class="alert callout">
            Stok barang berikut sudah (mulai) habis:
            <ul>
                <?php
                    foreach ($daftarAlertProduk as $produk)
                        echo '<li>'.$produk->nama.' (Stok: '.$produk->stok.')</li>';
                ?>
            </ul>
        </div>
        </div>
    </div>
<?php
    endif;

    if (!empty($messages)):
?>
    <div class="row">
        <div class="medium-12 columns">
<?php
        foreach ($messages as $message):
?>
    <div class="<?=$message['error'] ? 'alert' : 'success'?> callout" data-closable="slide-out-right">
        <p><?=$message['text']?></p>
        <button class="close-button" aria-label="Dismiss alert" type="button" data-close>
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php
        endforeach;
?>
        </div>
    </div>
<?php
    endif;
?>

<?= form_open('admin/produk/add') ?>
<div class="row">
    <div class="medium-4 columns">
        <label>Produk
            <input name="product" type="text">
        </label>
    </div>
    <div class="medium-2 columns">
        <label>Harga
            <input id="price" name="price" type="number" value="" max="999999">
        </label>
    </div>
    <div class="medium-2 column" style="max-width:100px">
        <label>Stok
            <input id="quantity" name="quantity" type="number" value="1" max="99">
        </label>
    </div>
    <div class="medium-1 column">
        <label>&nbsp;
        <button type="submit" class="button">Add</button>
        </label>
    </div>
    <div class="columns"></div>
</div>
<?= form_close() ?>

<?= form_open('admin/produk/update') ?>
<div class="row">
    <div class="medium-6 columns">
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th width="150">Harga</th>
                <th width="80">Stok</th>
                <!--<th width="80">Delete</th>-->
            </tr>
        </thead>
        <tbody id="formContent">
        <?php
        foreach ($daftarProduk as $produk):
            ?>
            <input type="hidden" name="productId[]" value="<?=$produk->id?>">
            <tr>
                <td><?=$produk->nama?></td>
                <td><input type="number" name="price[]" value="<?=$produk->harga?>" max="999999"></td>
                <td><input type="number" name="quantity[]" value="<?=$produk->stok?>" max="99"></td>
            </tr>
            <?php
        endforeach;
        ?>
        </tbody>
    </table>
    </div>
</div>
<div class="row">
    <div class="medium-5 columns"></div>
    <div class="medium-1 column">
        <button type="submit" class="success button">Update</button>
    </div>
</div>
<?= form_close() ?>


<div class="row">
    <h4>Rekap Penjualan</h4>
    <select id="selectHistory">
        <option value="0">Hari ini</option>
        <option value="30">1 Bulan Terakhir</option>
        <option value="90">3 Bulan Terakhir</option>
        <option value="">Semua</option>
    </select>
</div>
<div class="row">
    <canvas id="chartRekapPenjualan" width="400" height="400"></canvas>
</div>

<script src="<?=base_url('assets/js/vendor/Chart.js')?>"></script>
<script type="text/javascript">
    var chartRekapPenjualan;

    $(document).ready(function () {
        $("#price, #quantity").numeric({ decimal: false, negative: false });

        $("#selectHistory").on('change keyup', function() {
            var interval = $(this).val();
            updateChartPenjualan(interval);
        });

        $("#selectHistory").change();
    });
    
    function updateChartPenjualan (interval)
    {
        $.get("<?=site_url('rekap-penjualan')?>", { interval: interval },
            function (response)
            {
                if (response.error.length == 0)
                {
                    var labels = [];
                    var sets = [];
                    var colors = [];
                    var rekap = response.data;
                    rekap.forEach(function(produk)
                    {
                        labels[labels.length] = produk.nama;
                        sets[sets.length] = produk.jumlah;
                        colors[colors.length] = getRandomColor();
                    });

                    var data = {
                        labels: labels,
                        datasets: [{
                            data: sets,
                            backgroundColor: colors
                        }]
                    };

                    if (chartRekapPenjualan !== undefined)
                        chartRekapPenjualan.destroy();

                    var context = $("#chartRekapPenjualan");
                    var options = {
                        responsive: true,
                        maintainAspectRatio: false
                    };
                    chartRekapPenjualan = new Chart(context, {
                        type: 'doughnut',
                        data: data,
                        options: options
                    });
                }
                else
                {
                    alert(response.error);
                }
            }, "json"
        ).fail(function(e){
            alert('error: ' + e.message);
        });
    }

    function getRandomColor()
    {
        var letters = '0A1B2C3D4E5F6789';
        var color = '#';
        var r = 0;
        for (var i = 0; i < 6; i++ )
        {
            r += Math.floor(Math.random() * 1000);
            var idx = r % letters.length;
            color += letters[idx];
        }
        return color;
    }
</script>
