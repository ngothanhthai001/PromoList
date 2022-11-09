<?php

declare(strict_types=1);

namespace Marvelic\PromoLists\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Block\Widget\Form\Element\Gallery;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Page\Interceptor;
use Magento\Framework\Registry;
use Marvelic\PromoLists\Api\Data\CategoryInterface;
use Marvelic\PromoLists\Api\CategoryRepositoryInterface;
use Marvelic\PromoLists\Model\CategoryFactory;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Io\File;


abstract class Category extends Action
{
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;

    /**
     * @var CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var FileUploaderFactory
     */

    protected $_fileUploaderFactory;
    protected $_filesystem;
    protected $_fileDriver;

    protected $file;
    protected $dir;

    /**
     * @param CategoryFactory $categoryFactory
     * @param Registry $registry
     * @param Context $context
     */
    public function __construct(
        \Magento\MediaStorage\Model\File\UploaderFactory $fileUploaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Driver\File $fileDriver,
        File $file,
        DirectoryList $dir,
        CategoryFactory $categoryFactory,
        CategoryRepositoryInterface $categoryRepository,
        Registry $registry,
        Context $context
    ) {
        $this->categoryFactory = $categoryFactory;
        $this->categoryRepository = $categoryRepository;
        $this->registry = $registry;
        $this->context = $context;
        $this->_fileUploaderFactory = $fileUploaderFactory;
        $this->_filesystem = $filesystem;
        $this->_fileDriver = $fileDriver;
        $this->file = $file;
        $this->dir = $dir;
        parent::__construct($context);
    }
    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Marvelic_PromoLists::promo');
    }

    /**
     * @return \Marvelic\PromoLists\Model\Category
     */
    public function initModel()
    {
        $model = $this->categoryFactory->create();
        if ($this->getRequest()->getParam('id')) {
            $model->load($this->getRequest()->getParam('id'));
        }
        $this->registry->register('promotion', $model);
        return $model;

//        $model = $this->categoryRepository->create();
//        $id    = $this->getRequest()->getParam(CategoryInterface::ID);
//
//        if ($id && !is_array($id)) {
//            $model = $this->categoryRepository->get($id);
//        }
//
//        $this->registry->register('promotion', $model);
//
//        return $model;
    }

    /**
     * {@inheritdoc}
     * @param Page $resultPage
     *
     * @return Interceptor
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Marvelic_PromoLists::promolist');
        $resultPage->getConfig()->getTitle()->prepend(__('Categories'));

        return $resultPage;
    }
}
