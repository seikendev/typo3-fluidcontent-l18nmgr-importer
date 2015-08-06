<?php

spl_autoload_register();

/**
 * Created by PhpStorm.
 * User: Wojtek <woshiu@protonmail.ch>
 * Date: 04/08/2015
 */
class l18n {

    public $id;
    protected $ctype = 'fluidcontent_content';
    protected $lang_uid;
    /** @var mysqli $db_connection Connection to the db */
    public static $db_connection;
    protected $table = 'tt_content';

    /**
     * l18n constructor.
     * @param $args
     */
    public function __construct($args) {
        $this->lang_uid = (int)$args[1];
        if (count($args) > 2)
            $this->id = (int)$args[2];

        $db = new Db();
        self::$db_connection = $db->connect();
    }

    /*
     * Copy fluid contents from original language
     */
    public function copyFluidContentToNewLanguage() {
        $db = new Db();
        if ($this->id) {
            $select = "Select uid,tx_flux_parent from tt_content WHERE CType = 'fluidcontent_content' AND sys_language_uid=0  AND deleted=0 AND pid=$this->id";
        } else {
            $select = "Select uid,tx_flux_parent from tt_content WHERE CType = 'fluidcontent_content' AND sys_language_uid=0 AND deleted=0";
        }
        $parent_ids = [];
        if ($query = self::$db_connection->query($select)) {
            while ($row = $query->fetch_assoc()) {
                /** @var int $row fluid CE in original language */
                /** @var int $id uid of the newly created row for the fluid translation */
                $id = $db->duplicateRow(self::$db_connection,$this->table,"uid",$row['uid']);
                $parent_ids[$row['uid']] = $id;
                $update = "UPDATE tt_content SET sys_language_uid=$this->lang_uid,l18n_parent=".$row["uid"]." WHERE uid=$id";
                self::$db_connection->query($update);
            }
            $this->updateFluxChildsToNewLanguage($parent_ids);
            $query->free();
        }
    }

    protected function updateFluxChildsToNewLanguage($parent_ids) {
        foreach($parent_ids as $old=>$new) {
            $select_childs = "SELECT * from tt_content WHERE sys_language_uid=$this->lang_uid AND tx_flux_parent=$old";
            $select = self::$db_connection->query($select_childs);
            while ($row = $select->fetch_assoc()) {
                $update = "UPDATE tt_content SET tx_flux_parent=$new WHERE uid=".$row['uid'];
                echo "updateFluxChilds :: Updating uid=".$row['uid']."\n\r";
                self::$db_connection->query($update);
            }
        }
    }
}

/*
 * USAGE ::
 * First arg is language_id (required)
 * Second arg is page id (optional) , if not provided then go through all pages
 */
if (!$argv || count($argv) < 2) {
    echo "Usage : php l18n.php [language_id] [page_id|optional]";
    die();
}
$l18n = new l18n($argv);
$l18n->copyFluidContentToNewLanguage();