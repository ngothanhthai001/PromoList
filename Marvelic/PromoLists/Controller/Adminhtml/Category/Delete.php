<?php

namespace Marvelic\PromoLists\Controller\Adminhtml\Category;

use Exception;
use Marvelic\PromoLists\Controller\Adminhtml\Category;

class Delete extends Category
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $model = $this->initModel();

        $resultRedirect = $this->resultRedirectFactory->create();

        if ($model->getId()) {
            try {
/**---------------------------------------------delete img of category news -------------------------------------------------*/
                $id = $model->getId();
                $modelGa = $this->initModelGallery();
                $collGa = $modelGa->getCollection()->getData();
                foreach($collGa as $item){
                    if($item['entity_id'] == $id){
                        $img_desktop = $item['img_desktop'];
                        $img_mobile = $item['img_mobile'];
                    }
                }
                $flag_desktop = false;
                $flag_mobile = false;
                $path = $this->_filesystem->getDirectoryRead(\Magento\Framework\App\Filesystem\DirectoryList::MEDIA)->getAbsolutePath('marvelic_promolists/images/');

                foreach($collGa as $item){
                    if($item['entity_id'] != $id){
                        if($item['img_desktop'] == $img_desktop || $item['img_mobile'] == $img_desktop){
                            $flag_desktop = true;  break;
                        }
                    }
                }
                foreach($collGa as $item){
                    if($item['entity_id'] != $id){
                        if($item['img_mobile'] == $img_mobile || $item['img_desktop'] == $img_mobile){
                            $flag_mobile = true; break;

                        }
                    }
                }
                if(!$flag_desktop){
                 
                    $fileNamePath = $path . $img_desktop;
                    if($this->_fileDriver->isExists($fileNamePath))
                    $this->_fileDriver->deleteFile($fileNamePath);

                } 
                if(!$flag_mobile){
                    $fileNamePath = $path . $img_mobile;
                    if($this->_fileDriver->isExists($fileNamePath))
                    $this->_fileDriver->deleteFile($fileNamePath);

                }
                
                $modelGa->load($id);
                $modelGa->delete();
                
                
/**--------------------------------------------end delete img of category news ----------------------------------------------*/
                $model->delete();

                $this->messageManager->addSuccess(__('The category has been deleted.'));

                return $resultRedirect->setPath('*/*/');
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', ['id' => $model->getId()]);
            }
        } else {
            $this->messageManager->addError(__('This category no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }
    }
}
