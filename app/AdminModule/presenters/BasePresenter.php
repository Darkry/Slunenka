<?php
namespace AdminModule;
/**
 * BasePresenter for DarkAdmin -> all presenters in Admin extends from it
 *
 * @package DarkAdmin
 * @author Kryštof Selucký
 */
class BasePresenter extends \BasePresenter {

    /** @var \Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * sets the model for working with database (execute while the presenter is started)
     */
    protected function startup() {
        parent::startup();
        $this->em = $this->getService("database");

        \Nette\Forms\Container::extensionMethod('addDatePicker', function (\Nette\Forms\Container $container, $name, $label = NULL) {
            return $container[$name] = new \JanTvrdik\Components\DatePicker($label);
        });
    }

    /**
     * Sets my macros -> they are avaible in DarkAdmin now
     *
     * @param \Nette\Templating\Template $tpl
     * @return void
     */
    public function templatePrepareFilters($tpl)
    {
        $tpl->registerFilter($latte = new \Nette\Latte\Engine);
        $set=\Nette\Latte\Macros\MacroSet::install($latte->getCompiler());
        $set->addMacro('js', function ($node, $writer) {
            return $writer->write('?><script type="text/javascript" src="' .
                    '<?php echo %escape($basePath . "/DA/js/" . %node.word); ?>' .
                    '.js"></script><?php');
        });
        $set->addMacro('css', function ($node, $writer) {
            return $writer->write('?><link rel="stylesheet" media="screen,projection,tv" href="' .
                    '<?php echo %escape($basePath . "/DA/css/" . %node.word); ?>' .
                    '.css" type="text/css" /><?php');
        });
    }
}