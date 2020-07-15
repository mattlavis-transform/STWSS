<?php
class content
{
    // Class properties and methods go here
    public $unique_id               = "";
    public $id                      = "";
    public $sid                     = null;
    public $step_description        = "";
    public $step_howto_description  = "";
    public $step_url                = "";
    public $header_id               = null;
    public $subheader_id            = null;
    public $header_description      = "";
    public $subheader_description   = "";

    /* Fudge for import / export */
    public $trade_types                     = array();
    public $trade_types_and_headers         = array();

    public $header_id_import                = null;
    public $subheader_id_import             = null;
    public $header_description_import       = "";
    public $subheader_description_import    = "";

    public $header_id_export                = null;
    public $subheader_id_export             = null;
    public $header_description_export       = "";
    public $subheader_description_export    = "";


    public $errors                  = array();

    public $section_id              = null;
    public $linkage                 = array();
    public $link_options            = array();
    public $edit_page_title         = "";
    public $country_exclusions      = "";

    public function populate()
    {
        global $conn, $app;

        $this->id = get_querystring("id");
        if ($this->id == "") {
            $this->edit_page_title = "Create new content item " . $this->id;
            $this->button_face = "Create content item";
            $app->crumb_string = "Home|/;Content|/content;Create content item " . "|";
        } else {
            $this->edit_page_title = "View / edit content item " . $this->id;
            $this->button_face = "Update content item";
            $app->crumb_string = "Home|/;Content|/content;Content item " . $this->id . "|";

            $sql = "select id, step_description, step_howto_description, step_url
            from signposting_steps ss where id = $1;";
            pg_prepare($conn, "get_content", $sql);
            $result = pg_execute($conn, "get_content", array($this->id));
            $row_count = pg_num_rows($result);
            if (($result) && ($row_count > 0)) {
                $row = pg_fetch_array($result);
                $this->id = $row['id'];
                $this->step_description = $row['step_description'];
                $this->step_howto_description = $row['step_howto_description'];
                $this->step_url = $row['step_url'];
            }
            // Get the trade type links too
            $sql = "select trade_type, header_id, subheader_id
            from signposting_step_heading_assignment where signposting_step_id = $1";
            $stmt = uniqid();
            pg_prepare($conn, $stmt, $sql);
            $result = pg_execute($conn, $stmt, array($this->id));

            $row_count = pg_num_rows($result);
            if (($result) && ($row_count > 0)) {
                while ($row = pg_fetch_array($result)) {
                    $h = new reusable();
                    $h->trade_type = $row['trade_type'];
                    $h->header_id = $row['header_id'];
                    $h->subheader_id = $row['subheader_id'];
                    array_push($this->trade_types_and_headers, $h);
                }
            }

            foreach ($this->trade_types_and_headers as $t) {
                if ($t->trade_type == "IMPORT") {
                    array_push($this->trade_types, "IMPORT");
                    $this->header_id_import = $t->header_id;
                    $this->subheader_id_import = $t->subheader_id;
                }
                if ($t->trade_type == "EXPORT") {
                    array_push($this->trade_types, "EXPORT");
                    $this->header_id_export = $t->header_id;
                    $this->subheader_id_export = $t->subheader_id;
                }
            }
        }
    }

    public function get_link_options()
    {
        array_push($this->link_options, new data_item("section", "Section"));
        array_push($this->link_options, new data_item("chapter", "Chapter"));
        array_push($this->link_options, new data_item("commodity", "Commodity code"));
        array_push($this->link_options, new data_item("measure_type", "Measure type"));
        array_push($this->link_options, new data_item("document_code", "Document code"));
        //array_push($this->link_options, new data_item("trade_type", "Trade type"));
    }

    public function link()
    {
        $id = get_request("id");
        $link_type = get_request("link_type");

        switch ($link_type) {
            case "section":
                $this->link_section();
                break;
            case "chapter":
                $this->link_chapter();
                break;
            case "commodity":
                $this->link_commodity();
                break;
            case "measure_type":
                $this->link_measure_type();
                break;
            case "document_code":
                $this->link_document_code();
                break;
        }

        $c = new confirmation();
        $c->panel_title = "Content item " . $id . " has been successfully linked";
        $c->panel_body = "";
        $c->step1 = "<a class='govuk-link' href='/content/link_01.html?id=" . $id . "'>Link this content again</a>";
        $c->step2 = "<a class='govuk-link' href='/content/edit.html?id=" . $id . "#linkage'>View this content item</a>";
        $c->step3 = "<a class='govuk-link' href='/content'>View all content</a>";
        $c->step4 = "";
        $c->encrypt_data();
        $url = "/includes/confirm.html?data=" . $c->data_encrypted;
        header("Location: " . $url);
    }

    public function unlink()
    {
        $this->id = get_request("id");
        $sid = get_request("sid");
        $src = get_request("src");
        $link_type = get_request("link_type");

        switch ($link_type) {
            case "section":
                $this->unlink_section($sid);
                $url = "/sections/view.html?id=" . $this->id . "#content";
                break;
            case "chapter":
                $this->unlink_chapter($sid);
                $url = "/chapters/view.html?id=" . $this->id . "#content";
                break;
            case "commodity":
                $this->unlink_commodity($sid);
                $url = "/";
                break;
            case "measure_type":
                $this->unlink_measure_type($sid);
                $url = "/";
                break;
            case "document_code":
                $this->unlink_document_code($sid);
                $url = "/";
                break;
        }

        switch ($src) {
            case "content":
                $url = "/content/edit.html?id=" . $this->id . "#linkage";
                break;
            case "content_index":
                $url = "/content#row_" . $this->id;
                break;
            case "entity":
                $url = "/" . $src . "/edit.html?id=" . $this->id . "#linkage";
                break;
            case "entity_index":
                $url = "/" . application::get_plural($link_type);
                break;
            default:

                break;
        }
        header("Location: " . $url);
    }

    public function link_section()
    {
        global $conn;
        $this->id = get_querystring("id");
        $section_id = get_querystring("section");
        $sql = "INSERT INTO signposting_step_section_assignment
        (signposting_step_id, section_id, date_created)
        VALUES ($1, $2, current_timestamp)
        on conflict ON CONSTRAINT signposting_step_section_assignment_un DO NOTHING";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $this->id, $section_id
        ));
        $this->unblanket();
    }


    public function link_chapter()
    {
        global $conn;
        $this->id = get_querystring("id");
        $chapter = intval(get_querystring("chapter"));
        $sql = "INSERT INTO signposting_step_chapter_assignment
        (signposting_step_id, chapter_id, date_created)
        VALUES ($1, $2, current_timestamp)
        on conflict ON CONSTRAINT signposting_step_chapter_assignment_un DO NOTHING";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $this->id, $chapter
        ));
        $this->unblanket();
    }

    public function link_document_code()
    {
        global $conn;
        $this->id = get_querystring("id");
        $document_code = get_querystring("document_code");
        $sql = "INSERT INTO signposting_step_document_code_assignment
        (signposting_step_id, document_code, date_created)
        VALUES ($1, $2, current_timestamp)
        on conflict ON CONSTRAINT signposting_step_document_code_assignment_un DO NOTHING";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $this->id, $document_code
        ));
        $this->unblanket();
    }

    public function link_measure_type()
    {
        global $conn;
        $this->id = get_querystring("id");
        $measure_type = get_querystring("measure_type");
        $sql = "INSERT INTO signposting_step_measure_type_assignment
        (signposting_step_id, measure_type_id, date_created)
        VALUES ($1, $2, current_timestamp)
        on conflict ON CONSTRAINT signposting_step_measure_type_assignment_un DO NOTHING";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $this->id, $measure_type
        ));
        $this->unblanket();
    }

    public function link_commodity()
    {
        global $conn;
        $this->id = get_querystring("id");
        $commodity = new commodity();
        $action = get_querystring("action");
        if ($action == "create_content") {
            $commodity->goods_nomenclature_item_id = get_querystring("identifier");
            $commodity->goods_nomenclature_sid = get_querystring("sid");
        } else {
            $commodity->goods_nomenclature_item_id = get_querystring("commodity_code");
            $commodity->goods_nomenclature_sid = get_querystring("commodity");
        }

        if ($commodity->validate()) {
            // Insert the commodity code
            $sql = "INSERT INTO goods_nomenclatures
            (goods_nomenclature_sid, goods_nomenclature_item_id, description, number_indents, productline_suffix)
            VALUES ($1, $2, $3, $4, $5)
            on conflict ON CONSTRAINT goods_nomenclatures_pk DO NOTHING;";
            $command = uniqid();
            pg_prepare($conn, $command, $sql);
            pg_execute($conn, $command, array(
                $commodity->goods_nomenclature_sid,
                $commodity->goods_nomenclature_item_id,
                $commodity->description,
                $commodity->number_indents,
                $commodity->productline_suffix
            ));

            // Insert the step assignment
            $sql = "INSERT INTO signposting_step_commodity_assignment
            (signposting_step_id, goods_nomenclature_sid, date_created)
            VALUES ($1, $2, current_timestamp)
            on conflict ON CONSTRAINT signposting_step_commodity_assignment_un DO NOTHING";
            $command = uniqid();
            pg_prepare($conn, $command, $sql);
            pg_execute($conn, $command, array(
                $this->id, $commodity->goods_nomenclature_sid
            ));

            h1("found");
        } else {
            h1("not found");
        };
        $this->unblanket();
    }

    public function unlink_section($sid)
    {
        global $conn;
        $sql = "DELETE FROM signposting_step_section_assignment where id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $sid
        ));
    }

    public function unlink_chapter($sid)
    {
        global $conn;
        $sql = "DELETE FROM signposting_step_chapter_assignment where id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $sid
        ));
    }

    public function unlink_commodity($sid)
    {
        global $conn;
        $sql = "DELETE FROM signposting_step_commodity_assignment where id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $sid
        ));
    }

    public function unlink_measure_type($sid)
    {
        global $conn;
        $sql = "DELETE FROM signposting_step_measure_type_assignment where id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $sid
        ));
    }

    public function unlink_document_code($sid)
    {
        global $conn;
        $sql = "DELETE FROM signposting_step_document_code_assignment where id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        pg_execute($conn, $command, array(
            $sid
        ));
    }

    public function get_linkages($link_type)
    {
        global $conn;
        switch ($link_type) {
            case "section":
                $title = "Links to sections";
                $sql = "select sssa.id, s.numeral as identifier, s.title as description, '/sections/view.html?id=' || s.id as view_url, 'section' as link_type
                from signposting_step_section_assignment sssa, sections s
                where s.id = sssa.section_id 
                and signposting_step_id = $1
                order by section_id ;";
                $link_url = "/content/link_02.html?link_type=section&id=" . $this->id;
                break;

            case "chapter":
                $title = "Links to chapters";
                $sql = "select ssca.id, c.id as identifier, c.description, '/chapters/view.html?id=' || c.id as view_url, 'chapter' as link_type
                from signposting_step_chapter_assignment ssca, chapters c
                where cast(c.id as int) = ssca.chapter_id 
                and signposting_step_id = $1
                order by c.id ;";
                $link_url = "/content/link_02.html?link_type=chapter&id=" . $this->id;
                break;

            case "commodity":
                $title = "Links to commodity codes";
                $sql = "select ssca.id, gn.goods_nomenclature_item_id as identifier, gn.description,
                '/commodities/view.html?goods_nomenclature_item_id=' || gn.goods_nomenclature_item_id as view_url, 'commodity' as link_type
                from signposting_step_commodity_assignment ssca, goods_nomenclatures gn 
                where gn.goods_nomenclature_sid = ssca.goods_nomenclature_sid 
                and signposting_step_id = $1
                order by gn.goods_nomenclature_item_id;";
                $link_url = "/content/link_02.html?link_type=commodity&id=" . $this->id;
                break;

            case "measure_type":
                $title = "Links to measure types";
                $sql = "select ssmta.id, mt.measure_type_id as identifier, mt.description,
                '/measure_types/view.html?id=' || mt.measure_type_id as view_url,
                'measure_type' as link_type
                from signposting_step_measure_type_assignment ssmta, measure_types mt
                where mt.measure_type_id = ssmta.measure_type_id 
                and signposting_step_id = $1
                order by mt.measure_type_id;"; // This is dummy code
                $link_url = "/content/link_02.html?link_type=measure_type&id=" . $this->id;
                break;

            case "document_code":
                $title = "Links to document codes";
                $sql = "select ssdca.id, c.code as identifier, c.description, '/document_codes/view.html?id=' || c.code as view_url,
                'document_code' as link_type
                from signposting_step_document_code_assignment ssdca, certificates c
                where c.code = ssdca.document_code 
                and signposting_step_id = $1
                order by c.code;";
                $link_url = "/content/link_02.html?link_type=document_code&id=" . $this->id;
                break;
        }
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array($this->id));
        $this->linkage = array();
        $row_count = pg_num_rows($result);
        if ($result) {
            while ($row = pg_fetch_array($result)) {
                $obj = new reusable;
                $obj->id = $row['id'];
                $obj->identifier = $row['identifier'];
                $obj->description = $row['description'];
                $obj->view_url = $row['view_url'];
                $obj->link_type = $row['link_type'];
                array_push($this->linkage, $obj);
            }
            $this->show_linkage_table($title, $link_type, $link_url, $row_count);
        }
    }

    public function show_linkage_table($title, $link_type, $link_url, $row_count)
    {
?>
        <h3 class="govuk-heading-m"><?= $title ?></h3>
        <?php
        if ($row_count == 0) {
            echo ("<p>This content is not linked to any " . application::get_plural($link_type) . ".");
        } else {
        ?>
            <table class="govuk-table govuk-table--m">
                <!--<caption class="govuk-table__caption"><?= $title ?></caption>//-->
                <thead class="govuk-table__head">
                    <tr class="govuk-table__row">
                        <th scope="col" class="govuk-table__header">ID</th>
                        <th scope="col" class="govuk-table__header">Description</th>
                        <th scope="col" class="govuk-table__header r">Actions</th>
                    </tr>
                </thead>
                <tbody class="govuk-table__body">
                    <?php
                    $has_shown_financial_message = false;
                    foreach ($this->linkage as $l) {
                    ?>
                        <tr class="govuk-table__row">
                            <td class="govuk-table__cell"><?= $l->identifier ?></td>
                            <td class="govuk-table__cell"><a class="govuk-link" href="<?= $l->view_url ?>"><?= $l->description ?></a></td>
                            <td class="govuk-table__cell r">
                                <a class="govuk-link" href="/includes/routes.php?action=delete_content_linkage&src=content&link_type=<?= $link_type ?>&id=<?= $this->id ?>&sid=<?= $l->id ?>"><img src="/public/images/delete.png" /></a>

                            </td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
<?php
        }
        echo ("<p><a class='govuk-link' href='" . $link_url . "'>Link content to " . str_replace("_", " ", $link_type) . "</a></p>");
    }

    function check_for_links()
    {
        global $conn;

        $sql = "with cte as (
        select sssa.id, signposting_step_id, 'Section ' || s.numeral as entity_id, 1 as priority, s.title as description, 'section' as link_type
        from signposting_step_section_assignment sssa, sections s
        where sssa.section_id = s.id 
        union
        select ssca.id, signposting_step_id, 'Chapter ' || c.id as entity_id, 2 as priority, c.description, 'chapter' as link_type
        from signposting_step_chapter_assignment ssca, chapters c
        where ssca.chapter_id = cast(c.id as int)
        union
        select ssmta.id, signposting_step_id, 'Measure type ' || mt.measure_type_id as entity_id, 4 as priority, mt.description, 'measure_type' as link_type
        from signposting_step_measure_type_assignment ssmta, measure_types mt
        where ssmta.measure_type_id = mt.measure_type_id 
        union
        select ssdca.id, signposting_step_id, 'Document code ' || c.code as entity_id, 5 as priority, c.description, 'document_code' as link_type
        from signposting_step_document_code_assignment ssdca, certificates c
        where ssdca.document_code = c.code
        )
        select count(*) as total_count from cte where cte.signposting_step_id = $1";

        $stmt = uniqid();
        pg_prepare($conn, $stmt, $sql);
        $result = pg_execute($conn, $stmt, array($this->id));

        $row_count = pg_num_rows($result);
        $total_count = 0;
        if (($result) && ($row_count > 0)) {
            $row = pg_fetch_array($result);
            $total_count = $row["total_count"];
        }
        if ($total_count > 0) {
            return (true);
        } else {
            return (false);
        }
    }

    function update()
    {
        global $conn;

        $this->trade_types = get_request("trade_type");
        if (in_array("IMPORT", $this->trade_types)) {
            $this->header_id_import = get_request("header_import");
            $this->subheader_id_import = get_request("subheader_import");
        } else {
            $this->header_id_import = null;
            $this->subheader_id_import = null;
        }
        if (in_array("EXPORT", $this->trade_types)) {
            $this->header_id_export = get_request("header_export");
            $this->subheader_id_export = get_request("subheader_export");
        } else {
            $this->header_id_export = null;
            $this->subheader_id_export = null;
        }

        $this->step_description = get_request("step_description");
        $this->step_howto_description = get_request("step_howto_description");
        $this->step_url = get_request("step_url");
        $this->country_exclusions = get_request("country_exclusions");
        $this->parse_country_exclusions();

        // Do the validation
        $this->validate();

        $link_type = get_request("link_type");
        $this->id = get_request("id");

        // Save the existing data to an audit table
        $sql = "insert into signposting_steps_history
        select * from signposting_steps ss where ss.id = $1;";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Update the step
        $sql = "UPDATE signposting_steps SET
        step_description = $1,
        step_howto_description = $2,
        step_url = $3,
        date_updated = current_timestamp
        WHERE id = $4";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->step_description,
            $this->step_howto_description,
            $this->step_url,
            $this->id
        ));

        // Delete existing headers, trade type links etc.
        $sql = "DELETE FROM signposting_step_trade_type_assignment
        WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        if ($this->check_for_links()) {
            $blanket_apply = null;
        } else {
            $blanket_apply = true;
        }

        // Insert the import header / subheader link
        if (($this->header_id_import != "") && ($this->subheader_id_import != "")) {
            $sql = "INSERT INTO signposting_step_heading_assignment
            (signposting_step_id, trade_type, header_id, subheader_id, date_created)
            VALUES ($1, $2, $3, $4, current_timestamp)";
            $command = uniqid();
            pg_prepare($conn, $command, $sql);
            $result = pg_execute($conn, $command, array(
                $this->id,
                "IMPORT",
                $this->header_id_import,
                $this->subheader_id_import
            ));
            $this->assign_trade_type("IMPORT", $blanket_apply);
        }

        // Insert the export header / subheader link
        if (($this->header_id_export != "") && ($this->subheader_id_export != "")) {
            $sql = "INSERT INTO signposting_step_heading_assignment
            (signposting_step_id, trade_type, header_id, subheader_id, date_created)
            VALUES ($1, $2, $3, $4, current_timestamp)";
            $command = uniqid();
            pg_prepare($conn, $command, $sql);
            $result = pg_execute($conn, $command, array(
                $this->id,
                "EXPORT",
                $this->header_id_export,
                $this->subheader_id_export
            ));
            $this->assign_trade_type("EXPORT", $blanket_apply);
        }

        // Then redirect to the confirmation page
        $c = new confirmation();
        $c->panel_title = "Content item " . $this->id . " has been successfully updated";
        $c->panel_body = "";
        $c->step1 = "<a class='govuk-link' href='/content/edit.html?id=" . $this->id . "'>View this content item</a>";
        $c->step2 = "<a class='govuk-link' href='/content'>View all content</a>";
        $c->encrypt_data();
        $url = "/includes/confirm.html?data=" . $c->data_encrypted;
        header("Location: " . $url);
    }

    function validate()
    {
        // Do the validation
        $this->errors = array();
        if ($this->step_description == "") {
            array_push($this->errors, "step_description");
        }
        /* - This is actually not mandatory
        if ($this->step_howto_description == "") {
            array_push($this->errors, "step_howto_description");
        }*/
        if ($this->step_url == "") {
            array_push($this->errors, "step_url");
        }

        if (!in_array("IMPORT", $this->trade_types) && !in_array("EXPORT", $this->trade_types)) {
            array_push($this->errors, "trade_type");
        }

        if (in_array("IMPORT", $this->trade_types)) {
            if ($this->header_id_import == "0") {
                array_push($this->errors, "header_import");
            }
            if ($this->subheader_id_import == "0") {
                array_push($this->errors, "subheader_import");
            }
        }

        if (in_array("EXPORT", $this->trade_types)) {
            if ($this->header_id_export == "0") {
                array_push($this->errors, "header_export");
            }
            if ($this->subheader_id_export == "0") {
                array_push($this->errors, "subheader_export");
            }
        }

        if (count($this->errors) > 0) {
            $this->data = "";

            $_SESSION["step_description"] = $this->step_description;
            $_SESSION["step_howto_description"] = $this->step_howto_description;
            $_SESSION["step_url"] = $this->step_url;
            $_SESSION["header_id_import"] = $this->header_id_import;
            $_SESSION["subheader_id_import"] = $this->subheader_id_import;
            $_SESSION["header_id_export"] = $this->header_id_export;
            $_SESSION["subheader_id_export"] = $this->subheader_id_export;
            $_SESSION["country_exclusions"] = $this->country_exclusions;
            $_SESSION["trade_types"] = $this->trade_types;

            $this->data .= serialize($this->errors);
            $this->data_encrypted = SA_Encryption::encrypt_to_url_param($this->data);

            $url = "/content/edit.html?err=1&data=" . $this->data_encrypted;
            header("Location: " . $url);
            die();
        }
    }

    function create()
    {
        global $conn, $app;

        $this->trade_types = get_request("trade_type");
        if (in_array("IMPORT", $this->trade_types)) {
            $this->header_id_import = get_request("header_import");
            $this->subheader_id_import = get_request("subheader_import");
        } else {
            $this->header_id_import = null;
            $this->subheader_id_import = null;
        }
        if (in_array("EXPORT", $this->trade_types)) {
            $this->header_id_export = get_request("header_export");
            $this->subheader_id_export = get_request("subheader_export");
        } else {
            $this->header_id_export = null;
            $this->subheader_id_export = null;
        }

        $this->step_description = trim(get_request("step_description"));
        $this->step_howto_description = trim(get_request("step_howto_description"));
        $this->step_url = trim(get_request("step_url"));
        $this->country_exclusions = get_request("country_exclusions");

        // Do the validation
        $this->validate();

        $link_type = get_request("link_type");
        $sid = get_request("sid");

        // Insert the step
        $sql = "INSERT INTO signposting_steps
        (step_description, step_howto_description, step_url, date_created, user_id)
        VALUES ($1, $2, $3, current_timestamp, $4) RETURNING id;";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->step_description,
            $this->step_howto_description,
            $this->step_url,
            $app->user_id
        ));

        // Get the ID back
        if (($result) && (pg_num_rows($result) > 0)) {
            $row = pg_fetch_row($result);
            $this->id = $row[0];
        }

        // Insert the import header / subheader link
        if (($this->header_id_import != "") && ($this->subheader_id_import != "")) {
            $sql = "INSERT INTO signposting_step_heading_assignment
            (signposting_step_id, trade_type, header_id, subheader_id, date_created)
            VALUES ($1, $2, $3, $4, current_timestamp)";
            $command = uniqid();
            pg_prepare($conn, $command, $sql);
            $result = pg_execute($conn, $command, array(
                $this->id,
                "IMPORT",
                $this->header_id_import,
                $this->subheader_id_import
            ));
            $this->assign_trade_type("IMPORT", true);
        }


        // Insert the export header / subheader link
        if (($this->header_id_export != "") && ($this->subheader_id_export != "")) {
            $sql = "INSERT INTO signposting_step_heading_assignment
            (signposting_step_id, trade_type, header_id, subheader_id, date_created)
            VALUES ($1, $2, $3, $4, current_timestamp)";
            $command = uniqid();
            pg_prepare($conn, $command, $sql);
            $result = pg_execute($conn, $command, array(
                $this->id,
                "EXPORT",
                $this->header_id_export,
                $this->subheader_id_export
            ));
            $this->assign_trade_type("EXPORT", true);
        }


        // Insert the country exclusions, if needed
        /*
        if (count($this->country_exclusion_list) > 0) {
            foreach ($this->country_exclusion_list as  $country_code) {
                $sql = "INSERT INTO signposting_step_country_exclusions
                (signposting_step_id, country_code, date_created)
                VALUES ($1, $2, current_timestamp)";
                $command = uniqid();
                pg_prepare($conn, $command, $sql);
                $result = pg_execute($conn, $command, array(
                    $id,
                    $country_code
                ));
            }
        }
        */


        // Then insert the links to entities if needed
        switch ($link_type) {
            case "section":
                $sql = "INSERT INTO signposting_step_section_assignment
                (signposting_step_id, section_id, date_created)
                VALUES ($1, $2, current_timestamp)";
                $command = uniqid();
                pg_prepare($conn, $command, $sql);
                $result = pg_execute($conn, $command, array(
                    $this->id,
                    $sid
                ));
                break;

            case "chapter":
                $sql = "INSERT INTO signposting_step_chapter_assignment
                (signposting_step_id, chapter_id, date_created)
                VALUES ($1, $2, current_timestamp)";
                $command = uniqid();
                pg_prepare($conn, $command, $sql);
                $result = pg_execute($conn, $command, array(
                    $this->id,
                    $sid
                ));
                break;

            case "commodity":
                $this->link_commodity();
                $sql = "INSERT INTO signposting_step_commodity_assignment
                (signposting_step_id, goods_nomenclature_sid, date_created)
                VALUES ($1, $2, current_timestamp)";
                $command = uniqid();
                pg_prepare($conn, $command, $sql);
                $result = pg_execute($conn, $command, array(
                    $this->id,
                    $sid
                ));
                break;

            case "measure_type":
                $sql = "INSERT INTO signposting_step_measure_type_assignment
                (signposting_step_id, measure_type_id, date_created)
                VALUES ($1, $2, current_timestamp)";
                $command = uniqid();
                pg_prepare($conn, $command, $sql);
                $result = pg_execute($conn, $command, array(
                    $this->id,
                    $sid
                ));
                break;
            case "document_code":
                $sql = "INSERT INTO signposting_step_document_code_assignment
                (signposting_step_id, document_code, date_created)
                VALUES ($1, $2, current_timestamp)";
                $command = uniqid();
                pg_prepare($conn, $command, $sql);
                $result = pg_execute($conn, $command, array(
                    $this->id,
                    $sid
                ));
                break;

            case "trade_type":
                $sql = "INSERT INTO signposting_step_trade_type_assignment
                (signposting_step_id, trade_type, date_created)
                VALUES ($1, $2, current_timestamp)";
                $command = uniqid();
                pg_prepare($conn, $command, $sql);
                $result = pg_execute($conn, $command, array(
                    $this->id,
                    $sid
                ));
                break;
        }

        // Then redirect to the confirmation page
        $c = new confirmation();
        $c->panel_title = "Content item " . $this->id . " has been successfully created";
        $c->panel_body = "";
        $c->step1 = "<a class='govuk-link' href='/content/link_01.html?id=" . $this->id . "'>Link to this content</a>";
        $c->step2 = "<a class='govuk-link' href='/content/edit.html?id=" . $this->id . "'>View this content item</a>";
        $c->step3 = "<a class='govuk-link' href='/content'>View all content</a>";
        $c->step4 = "<a class='govuk-link' href='/content/edit.html'>Create more content</a>";
        $c->encrypt_data();
        $url = "/includes/confirm.html?data=" . $c->data_encrypted;
        header("Location: " . $url);
    }

    function assign_trade_type($trade_type, $blanket_apply)
    {
        global $conn;
        // Insert the trade type(s)
        $sql = "INSERT INTO signposting_step_trade_type_assignment
        (signposting_step_id, trade_type, blanket_apply, date_created)
        VALUES ($1, $2, $3, current_timestamp)";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id,
            $trade_type,
            $blanket_apply
        ));
    }

    function unblanket()
    {
        global $conn;
        // Called when the content item is updated to link to any reference data
        $sql = "UPDATE signposting_step_trade_type_assignment
        SET blanket_apply = false WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));
    }

    function delete()
    {
        global $conn;

        $this->id = get_request("id");
        $yes_no = get_request("yes_no");
        if ($yes_no != "yes") {
            $url = "/content";
            header("Location: " . $url);
            return;
        }

        // Delete the country exlusions
        $sql = "DELETE FROM signposting_step_country_exclusions WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Delete the section assignments
        $sql = "DELETE FROM signposting_step_section_assignment WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Delete the chapter assignments
        $sql = "DELETE FROM signposting_step_chapter_assignment WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Delete the commodity assignments
        $sql = "DELETE FROM signposting_step_commodity_assignment WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Delete the measure type assignments
        $sql = "DELETE FROM signposting_step_measure_type_assignment WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Delete the document code assignments
        $sql = "DELETE FROM signposting_step_document_code_assignment WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Delete the trade type assignments
        $sql = "DELETE FROM signposting_step_trade_type_assignment WHERE signposting_step_id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Delete the content itself
        $sql = "DELETE FROM signposting_steps WHERE id = $1";
        $command = uniqid();
        pg_prepare($conn, $command, $sql);
        $result = pg_execute($conn, $command, array(
            $this->id
        ));

        // Then show the confirmation page
        $c = new confirmation();
        $c->panel_title = "Content item " . $this->id . " has been deleted";
        $c->panel_body = "";
        $c->step1 = "<a class='govuk-link' href='/content'>View all content</a>";
        $c->encrypt_data();
        $url = "/includes/confirm.html?data=" . $c->data_encrypted;
        header("Location: " . $url);
    }

    function parse_country_exclusions()
    {
        $this->country_exclusions = str_replace(" ", "", $this->country_exclusions);
        $this->country_exclusions = trim($this->country_exclusions);
        if ($this->country_exclusions == "") {
            $this->country_exclusion_list = array();
        } else {
            $this->country_exclusion_list = explode(",", $this->country_exclusions);
        }
    }
}
