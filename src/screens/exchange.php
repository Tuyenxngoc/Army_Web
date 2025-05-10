<?php
$tab = isset($_GET['tab']) ? $_GET['tab'] : '';
?>
<div class="overlay"></div>
<form class="text-center fw-semibold fs-5">
    <select class="w-50 rounded-3 btn-sm input-ex" id="exchangeType" name="exchangeType">
        <option value="exchange" class="<?php echo $tab == "exchange" ? "active" : ""; ?>" <?php echo $tab == "exchange" ? "selected" : ""; ?>>Vui lòng chọn dịch vụ</option>
        <option value="gold" class="<?php echo $tab == "gold" ? "active" : ""; ?>" <?php echo $tab == "gold" ? "selected" : ""; ?>>Đổi Lượng</option>
        <option value="coin" class="<?php echo $tab == "coin" ? "active" : ""; ?>" <?php echo $tab == "coin" ? "selected" : ""; ?>>Đổi Xu</option>
    </select>
</form>

<div id="noti" style="text-align: center;"></div>
<div id="exchangeContent"></div>

<?php
switch ($tab) {
    case "gold":
        include_once('exchange/exchange-gold.php');
        break;
    case "coin":
        include_once('exchange/exchange-coin.php');
        break;
    default:
        include_once('exchange.php'); 
        break;
}
?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var selectElement = document.getElementById('exchangeType');
        var defaultOptionValue = 'exchange';
        var baseUrl = '/exchange';
        
        function updateSelection() {
            var selectedValue = selectElement.value;
            if (selectedValue === defaultOptionValue) {
                return;
            }
            if (selectedValue) {
                window.location.href = baseUrl + '/' + selectedValue;
            }
        }
        if (selectElement.value === defaultOptionValue) {
            selectElement.querySelector('option[value="exchange"]').style.display = 'block';
        } else {
            selectElement.querySelector('option[value="exchange"]').style.display = 'none';
        }

        selectElement.addEventListener('change', updateSelection);
    });
</script>

<style>
    label {
        display: block;
        margin-bottom: 10px;
        font-size: 18px;
        color: #333;
    }

    select {
        background-color: #043a34;
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 4px;
        border: 1px solid #ccc;
        font-size: 16px;
    }

    select:focus {
        border-color: #aaa;
        box-shadow: 0 0 1px 3px rgba(59, 153, 252, .7);
        box-shadow: 0 0 0 3px -moz-mac-focusring;
        color: #222;
    }

    .input-ex {
        padding: 10px;
        border-radius: 10px !important;
        border: 2px solid gold !important;
        box-shadow: 0 0 10px rgba(255, 215, 0, .8) !important;
        color: white !important;
        font-weight: 700;
    }

    select option:checked {
        font-weight: 700;
    }
</style>
