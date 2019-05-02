<?php


Class Category extends CategoryCore
{
    public function deleteImage2($force_delete = false)
    {
        if (!$this->id) {
            return false;
        }

        if ($force_delete || !$this->hasMultishopEntries()) {
            /* Deleting object images and thumbnails (cache) */
            if ($this->image_dir) {
                if (file_exists($this->image_dir.$this->id.'_second.'.$this->image_format)
                    && !unlink($this->image_dir.$this->id.'_second.'.$this->image_format)) {
                    return false;
                }
            }
            if (file_exists(_PS_TMP_IMG_DIR_.$this->def['table'].'_'.$this->id.'_second.'.$this->image_format)
                && !unlink(_PS_TMP_IMG_DIR_.$this->def['table'].'_'.$this->id.'_second.'.$this->image_format)) {
                return false;
            }
            if (file_exists(_PS_TMP_IMG_DIR_.$this->def['table'].'_mini_'.$this->id.'_second.'.$this->image_format)
                && !unlink(_PS_TMP_IMG_DIR_.$this->def['table'].'_mini_'.$this->id.'_second.'.$this->image_format)) {
                return false;
            }

            $types = ImageType::getImagesTypes();
            foreach ($types as $image_type) {
                if (file_exists($this->image_dir.$this->id.'_second-'.stripslashes($image_type['name']).'.'.$this->image_format)
                    && !unlink($this->image_dir.$this->id.'_second-'.stripslashes($image_type['name']).'.'.$this->image_format)) {
                    return false;
                }
            }
        }

        return true;
    }
    public function getSubCategories($idLang, $active = true)
    {
        $sqlGroupsWhere = '';
        $sqlGroupsJoin = '';
        if (Group::isFeatureActive()) {
            $sqlGroupsJoin = 'LEFT JOIN `'._DB_PREFIX_.'category_group` cg ON (cg.`id_category` = c.`id_category`)';
            $groups = FrontController::getCurrentCustomerGroups();
            $sqlGroupsWhere = 'AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '='.(int) Group::getCurrent()->id);
        }

        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
		SELECT c.*, cl.`id_lang`, cl.`name`, cl.`description`, cl.`link_rewrite`, cl.`meta_title`, cl.`meta_keywords`, cl.`meta_description`
		FROM `'._DB_PREFIX_.'category` c
		'.Shop::addSqlAssociation('category', 'c').'
		LEFT JOIN `'._DB_PREFIX_.'category_lang` cl ON (c.`id_category` = cl.`id_category` AND `id_lang` = '.(int) $idLang.' '.Shop::addSqlRestrictionOnLang('cl').')
		'.$sqlGroupsJoin.'
		WHERE `id_parent` = '.(int) $this->id.'
		'.($active ? 'AND `active` = 1' : '').'
		'.$sqlGroupsWhere.'
		GROUP BY c.`id_category`
		ORDER BY `level_depth` ASC, category_shop.`position` ASC');

        foreach ($result as &$row) {
            $row['id_image'] = Tools::file_exists_cache($this->image_dir.$row['id_category'].'.jpg') ? (int) $row['id_category'] : Language::getIsoById($idLang).'-default';
            $row['id_image2'] = Tools::file_exists_cache(_PS_CAT_IMG_DIR_.$row['id_category'].'_second.jpg') ? (int)$row['id_category'] .'_second' : Language::getIsoById($idLang).'-default';
            $row['legend'] = 'no picture';
        }

        return $result;
    }

}