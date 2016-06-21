<?php
/**
 * User: RN
 * Date: 6/21/2016
 * Time: 09:55
 */
?>

<div class="row">
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
        <a id="add" href="javascript:void(0)" class="button">Add</a>
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


<?= form_close() ?>

<script type="text/javascript">
    var totalHarga = 0;
    $(document).ready(function () {
        $("#quantity").numeric({ decimal: false, negative: false });

        $("#add").click(function () {
            var selectedProduct = $("#selectProduct option:selected");
            $("#formContent").append('<input type="hidden" value="'+ selectedProduct.val() +'" name="products[]">');

            var htmlParts = selectedProduct.html().split('@');
            var name = htmlParts[0].trim();
            var harga = htmlParts[1].trim();

            var hargaUnit = parseInt(selectedProduct.data('price'));
            var jumlah = parseInt($("#quantity").val());
            if (isNaN(jumlah))
                jumlah = 1;

            var subtotal = hargaUnit * jumlah;

            var row = '<tr>' +
                    '<td>' + name + '</td>' +
                    '<td>' + harga + '</td>' +
                    '<td>' + jumlah + '</td>' +
                    '<td>Rp ' + subtotal + '</td>' +
                '</tr>';
            $("#formContent").append(row);

            totalHarga += subtotal;
            $("#totalHarga").html(totalHarga);
        });
    });
</script>
