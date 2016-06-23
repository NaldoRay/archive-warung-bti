<?php
/**
 * User: RN
 * Date: 6/21/2016
 * Time: 09:55
 */

/** @var array $daftarPenjualan */
?>

<div class="row" xmlns="http://www.w3.org/1999/html">
    <div class="small-6 medium-5 columns">
        <label>Produk
            <select id="selectProduct">
                <?php
                foreach ($daftarProduk as $produk):
                    ?>
                    <option value="<?=$produk->id?>" data-price="<?=$produk->harga?>"><?=$produk->nama?> @ Rp <?=number_format($produk->harga)?></option>
                    <?php
                endforeach;
                ?>
            </select>
        </label>
    </div>
    <div class="medium-2 column" style="max-width:100px">
        <label>Jumlah
            <input id="quantity" type="number" value="1" max="99">
        </label>
    </div>
    <div class="medium-1 column">
        <label>&nbsp;
        <button id="add" type="button" class="button">Add</button>
        </label>
    </div>
    <div class="columns"></div>
</div>

<?= form_open('') ?>
<div class="row">
    <div class="medium-6 columns">
    <table>
        <thead>
            <tr>
                <th>Nama</th>
                <th width="150">Harga</th>
                <th width="80">Jumlah</th>
                <th width="200">Subtotal</th>
                <th width="80">Delete</th>
            </tr>
        </thead>
        <tbody id="formContent"></tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total</td>
                <td id="totalHarga" colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    </div>
</div>
<div class="row">
    <div class="medium-5 columns"></div>
    <div class="medium-1 column">
        <button id="save" type="submit" class="success button">Bayar</button>
    </div>
</div>
<?= form_close() ?>


<div class="row">
    <div class="medium-12 columns">
        <h3>History</h3>
    </div>
</div>
<div class="row">
    <div class="medium-10 columns">
        <table>
            <thead>
            <tr>
                <th width="80" class="align-center">No</th>
                <th width="200">Nama</th>
                <th width="150" class="align-right">Harga</th>
                <th width="80" class="align-right">Jumlah</th>
                <th>Tanggal</th>
            </tr>
            </thead>
            <tbody id="historyBody">
            <?php
                $noUrut = 1;
                $lastId = null;
                foreach ($daftarPenjualan as $penjualan):
                    $no;
                    $tanggalPenjualan;
                    $idPenjualan = $penjualan->id_penjualan;
                    if ($idPenjualan == $lastId)
                    {
                        $no = '';
                        $tanggalPenjualan = '';
                    }
                    else
                    {
                        $no = $noUrut++;
                        $tanggalPenjualan = $penjualan->tanggal_penjualan;

                        $lastId = $idPenjualan;
                    }
            ?>
                <tr>
                    <td class="align-center"><?=$no?></td>
                    <td><?=$penjualan->nama?></td>
                    <td class="align-right">Rp <?=number_format($penjualan->harga)?></td>
                    <td class="align-right"><?=$penjualan->jumlah?></td>
                    <td><?=$tanggalPenjualan?></td>
                </tr>
            <?php
                endforeach;
            ?>
            </tbody>
        </table>
    </div>
</div>

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

<br /><br />
<div class="row">
    <h4>Rekap Pendapatan</h4>
</div>
<div class="row">
    <canvas id="chartRekapPendapatan" height="350" width="700"></canvas>
</div>

<script src="<?=base_url('assets/js/vendor/Chart.js')?>"></script>
<script type="text/javascript">
    var nextRowId = 0;
    var totalHarga = 0;
    var chartRekapPenjualan;
    var chartRekapPendapatan;

    var months = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember' ];

    $(document).ready(function () {
        $("#quantity").numeric({ decimal: false, negative: false });

        $("#add").click(function () {
            var selectedProduct = $("#selectProduct option:selected");

            var htmlParts = selectedProduct.html().split("@");
            var name = htmlParts[0].trim();
            var harga = htmlParts[1].trim();

            var hargaUnit = parseInt(selectedProduct.data("price"));
            var jumlah = parseInt($("#quantity").val());
            if (isNaN(jumlah))
                jumlah = 1;

            var subtotal = hargaUnit * jumlah;

            var rowId = nextRowId++;
            var row = '<tr id="buy-' + rowId + '">' +
                    "<td>" + name + "</td>" +
                    "<td>" + harga + "</td>" +
                    "<td>" + jumlah + "</td>" +
                    "<td>Rp " + subtotal.toLocaleString() + "</td>" +
                    '<td>' +
                        '<button type="button" class="deleteButton alert button" onclick="deleteBuyRow(' + rowId + ',' + subtotal + ')">&#10006;</button>' +
                    '</td>' +
                "</tr>";
            $("#formContent").append(row);

            totalHarga += subtotal;
            $("#totalHarga").html("Rp " + totalHarga.toLocaleString());

            $("#formContent").append('<input id="product-' + rowId + '" type="hidden" value="'+ selectedProduct.val() +'" name="product[]">');
            $("#formContent").append('<input id="quantity-' + rowId + '" type="hidden" value="'+ jumlah +'" name="quantity[]">');
        });

        $("#selectHistory").on('change keyup', function() {
            var interval = $(this).val();
            updateChartPenjualan(interval);
        });

        $("#selectHistory").change();
        updateChartPendapatan(3);
    });

    function deleteBuyRow (rowId, subtotal)
    {
        var tr = $("#buy-" + rowId);
        tr.fadeOut(500, function() {
            tr.remove();
            $("#product-" + rowId).remove();
            $("#quantity-" + rowId).remove();

            totalHarga -= subtotal;
            $("#totalHarga").html("Rp " + totalHarga.toLocaleString());
        });
    }
    
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

    function updateChartPendapatan (interval)
    {
        $.get("<?=site_url('rekap-pendapatan')?>", { interval: interval },
            function (response)
            {
                if (response.error.length == 0)
                {
                    var labels = [];
                    var sets = [];

                    var rekap = response.data;
                    var i = 0;
                    rekap.forEach(function(pendapatan)
                    {
                        var idx = rekap.length-i;
                        labels[idx] = months[pendapatan.month];
                        sets[idx] = pendapatan.total;
                        i++;
                    });

                    var data = {
                        labels: labels,
                        datasets: [{
                            label: 'Pendapatan 3 bulan terakhir',
                            fill: false,
                            lineTension: 0,
                            backgroundColor: "rgba(75,192,192,0.4)",
                            borderColor: "rgba(75,192,192,1)",
                            borderCapStyle: 'butt',
                            borderDash: [],
                            borderDashOffset: 0.0,
                            borderJoinStyle: 'miter',
                            pointBorderColor: "rgba(75,192,192,1)",
                            pointBackgroundColor: "#fff",
                            pointBorderWidth: 1,
                            pointHoverRadius: 5,
                            pointHoverBackgroundColor: "rgba(75,192,192,1)",
                            pointHoverBorderColor: "rgba(220,220,220,1)",
                            pointHoverBorderWidth: 2,
                            pointRadius: 1,
                            pointHitRadius: 10,
                            data: sets
                        }]
                    };

                    if (chartRekapPendapatan !== undefined)
                        chartRekapPendapatan.destroy();

                    var context = $("#chartRekapPendapatan");
                    var options = {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            yAxes: [{
                                ticks: {
                                    // Create scientific notation labels
                                    callback: function(value) {
                                        return "Rp " + value.toLocaleString();
                                    }
                                }
                            }]
                        },
                        tooltips: {
                            callbacks: {
                                title: function(tooltipItems, data) {
                                    return 'Pendapatan bulanan';
                                },
                                label: function(tooltipItem, data) {
                                    return tooltipItem.xLabel + ' - Rp ' + tooltipItem.yLabel.toLocaleString();
                                }
                            }
                        }
                    };
                    chartRekapPendapatan = new Chart(context, {
                        type: 'line',
                        data: data,
                        options: options
                    });
                    Chart.defaults.global.title.callbacks
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
