<?php
/**
 * User: RN
 * Date: 6/21/2016
 * Time: 09:55
 */
?>

<div class="row" xmlns="http://www.w3.org/1999/html">
    <div class="small-6 medium-5 columns">
        <select id="selectProduct">
            <?php
            foreach ($daftarProduk as $produk):
                ?>
                <option value="<?=$produk->id?>" data-price="<?=$produk->harga?>"><?=$produk->nama?> @ Rp <?=$produk->harga?></option>
                <?php
            endforeach;
            ?>
        </select>
    </div>
    <div class="medium-1 column">
        <input id="quantity" type="number" value="1">
    </div>
    <div class="medium-1 column">
        <button id="add" type="button" class="button">Add</button>
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
            </tr>
        </thead>
        <tbody id="formContent"></tbody>
        <tfoot>
            <tr>
                <td colspan="3">Total</td>
                <td id="totalHarga"></td>
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
                <th width="80">No</th>
                <th width="200">Nama</th>
                <th width="150">Harga</th>
                <th width="80">Jumlah</th>
                <th>Tanggal</th>
            </tr>
            </thead>
            <tbody id="historyBody">
            <?php
                $noUrut = 1;
                $lastId = null;
                foreach ($daftarPenjualan as $penjualan):
                    $no;
                    $idPenjualan = $penjualan->id_penjualan;
                    if ($idPenjualan == $lastId)
                        $no = '';
                    else
                    {
                        $no = $noUrut++;
                        $lastId = $idPenjualan;
                    }
            ?>
                <tr>
                    <td><?=$no?></td>
                    <td><?=$penjualan->nama?></td>
                    <td>Rp <?=$penjualan->harga?></td>
                    <td><?=$penjualan->jumlah?></td>
                    <td><?=$penjualan->tanggal_penjualan?></td>
                </tr>
            <?php
                endforeach;
            ?>
            </tbody>
        </table>
    </div>
</div>

<script type="text/javascript">
    var totalHarga = 0;

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

            var row = "<tr>" +
                    "<td>" + name + "</td>" +
                    "<td>" + harga + "</td>" +
                    "<td>" + jumlah + "</td>" +
                    "<td>Rp " + subtotal + "</td>" +
                "</tr>";
            $("#formContent").append(row);

            totalHarga += subtotal;
            $("#totalHarga").html(totalHarga);

            $("#formContent").append('<input type="hidden" value="'+ selectedProduct.val() +'" name="product[]">');
            $("#formContent").append('<input type="hidden" value="'+ jumlah +'" name="quantity[]">');
        });
    });
</script>
