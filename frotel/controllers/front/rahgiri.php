<?php
class FrotelRahgiriModuleFrontController extends ModuleFrontController
{
    public $ssl = true;
    public function initContent()
	{
        // Disable left and right column
		$this->display_column_left = false;
		$this->display_column_right = false;

        parent::initContent();

        $this->context->smarty->assign(array(
            'ajaxurl' => _MODULE_DIR_ . 'frotel/views/ajax/ajaxprocessFrotel.php',
        ));

        $this->setTemplate('rahgiri.tpl');
    }

}