<!doctype html>
<html>
<head>

    <!-- CSS – Tailwind CSS 4 + DaisyUI 5 (replaces Bootstrap) -->
    <link rel="stylesheet" href="<?php echo base_url().'css/tailwind.css'; ?>">
    <link rel="stylesheet" href="<?php echo base_url().'css/bootstrap-icons.min.css'; ?>">
    <link rel="stylesheet" href="<?php echo base_url().'css/custom.css'; ?>">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="<?php echo base_url().'css/languages.min.css'; ?>">
    <link rel="stylesheet" href="<?php echo base_url().'css/jquery-ui.css'; ?>" />

    <!-- Cookie consent -->
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/cookieconsent2/3.0.3/cookieconsent.min.css" />

    <!-- Bank slider -->

    <!-- Leaflet map -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.min.css" crossorigin="">

    <!-- Dynamic Meta Description -->
    <?php
        $controller_name = $this->uri->segment(1);
        $key = $controller_name . '_meta_desc';

        if (isset($this->lang->line('home')[$key])) {
            $description = $this->lang->line('home')[$key];
        } else {
            $description = "Ski-Manager free online game. Manage your ski resort.";
        }

        echo '<meta name="description" content="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '">';
    ?>

    <!-- Required Meta -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />

    <!-- Popunder Script -->
    <script data-cfasync="false" type="text/javascript" src="https://vetofellowshipfly.com/ce/37/c2/ce37c287ef68836c85b2c1d396361fd6.js"></script>
</head>
<body>
