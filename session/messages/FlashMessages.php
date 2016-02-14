<?php

    namespace nox\session\messages;

    use Yii;
    use yii\base\Object;

    /**
     * Class FlashMessages
     *
     * @category         Flash Messages
     * @author           Jonatas Sas <atendimento@jsas.com.br>
     *
     * @package          nox\session\messages
     *
     * @todo-project     Adicionar, quando necessário, o campo o nome do atributo que disparou o erro.
     * @todo-project     Criar forma de sobrepor o título da mensagem.
     */
    class FlashMessages extends Object
    {
        const TYPE_INFO    = 'info';
        const TYPE_SUCCESS = 'success';
        const TYPE_WARNING = 'warning';
        const TYPE_ERROR   = 'danger';

        const TYPE_INFO_TITLE    = 'Informação';
        const TYPE_SUCCESS_TITLE = 'Sucesso';
        const TYPE_WARNING_TITLE = 'Atenção';
        const TYPE_ERROR_TITLE   = 'Erro';

        const TYPE_INFO_COLOR    = '#31708f';
        const TYPE_SUCCESS_COLOR = '#3c763d';
        const TYPE_WARNING_COLOR = '#8a6d3b';
        const TYPE_ERROR_COLOR   = '#a94442';

        const TYPE_INFO_CLASS    = 'info';
        const TYPE_SUCCESS_CLASS = 'success';
        const TYPE_WARNING_CLASS = 'warning';
        const TYPE_ERROR_CLASS   = 'danger';

        const TYPE_ICON_ERROR    = '@images/ico-ball-error.png';
        const TYPE_ICON_WARNING  = '@images/ico-ball-warning.png';
        const TYPE_ICON_SUCCESS  = '@images/ico-ball-success.png';
        const TYPE_ICON_INFO     = '@images/ico-ball-info.png';

        /**
         * @var bool
         */
        public $hasMessage = false;

        /**
         * @var string
         */
        public $type = self::TYPE_INFO;

        /**
         * @var string
         */
        public $message = '';

        /**
         * @var string
         */
        public static $frontendMessageTemplate /** @lang HTML */ = '<div class="alert alert-{type}" role="alert">
    <h3 class="text-left text-uppercase" style="margin:0 0 5px -1px;">{title}</h3>
    <blockquote style="margin:0;border-left:5px solid {color};">
        {contents}
    </blockquote>
    <div class="clear"></div>
</div>';

        /**
         * @var string
         */
        public static $backendMessageTemplate /** @lang HTML */ = '<div class="alert alert-{type}" role="alert">
    <h3 class="text-left text-uppercase" style="margin:0 0 5px -1px;">{title}</h3>
    <blockquote style="margin:0;border-left:5px solid {color};">
        {contents}
    </blockquote>
    <div class="clear"></div>
</div>';

        /**
         * @param string   $type
         * @param static[] $messages
         * @param bool     $frontend
         * @param bool     $forceNoList
         *
         * @return string
         */
        public static function write($type, $messages, $frontend = false, $forceNoList = true)
        {
            $title    = self::getTitle($type);
            $icon     = '';
            $color    = self::getColor($type);
            $contents = '';
            $useList  = false;

            if ($frontend) {
                $icon = Yii::getAlias(self::getIcon($type));
            }

            if (!is_array($messages)) {
                return '';
            } else {
                if (count($messages) > 1) {
                    $useList = (((bool)$forceNoList) ? false : true);
                }

                foreach ($messages as $line) {
                    if (!$line instanceof static) {
                        continue;
                    }

                    $contents .= (($useList) ? '<li style="font-size:14px;font-weight:normal;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> ' : '').$line->message.(($useList) ? '</li>' : '');
                }
            }

            if (!empty($contents)) {
                $contents = (($useList) ? '<ul class="list-unstyled">' : '<p style="font-size:14px;font-weight:normal;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> ').$contents.(($useList) ? '</ul>' : '</p>');
            } else {
                return '';
            }

            $templateVariables = ['type', 'title', 'color', 'contents'];
            $template          = static::$backendMessageTemplate;

            if ((bool)$frontend) {
                $template = static::$frontendMessageTemplate;
            }

            foreach ($templateVariables as $variable) {
                $template = preg_replace("/\{{$variable}\}/", $$variable, $template);
            }

            return $template;
        }

        /**
         * @return array
         */
        public static function getAvailableTypes()
        {
            return [self::TYPE_INFO, self::TYPE_SUCCESS, self::TYPE_WARNING, self::TYPE_ERROR];
        }

        /**
         * @param string $type
         *
         * @return string
         */
        public static function getIcon($type = self::TYPE_INFO)
        {
            switch ($type) {
                case self::TYPE_INFO: {
                    return self::TYPE_ICON_INFO;
                }

                case self::TYPE_WARNING: {
                    return self::TYPE_ICON_WARNING;
                }

                case self::TYPE_SUCCESS: {
                    return self::TYPE_ICON_SUCCESS;
                }

                case self::TYPE_ERROR: {
                    return self::TYPE_ICON_ERROR;
                }

                default: {
                    return self::TYPE_ICON_INFO;
                }
            }
        }

        /**
         * @param string $type
         *
         * @return string
         */
        public static function getTitle($type)
        {
            switch ($type) {
                case self::TYPE_INFO: {
                    return self::TYPE_INFO_TITLE;
                }

                case self::TYPE_SUCCESS: {
                    return self::TYPE_SUCCESS_TITLE;
                }

                case self::TYPE_WARNING: {
                    return self::TYPE_WARNING_TITLE;
                }

                case self::TYPE_ERROR: {
                    return self::TYPE_ERROR_TITLE;
                }

                default: {
                    return self::TYPE_INFO_TITLE;
                }
            }
        }

        /**
         * @param string $type
         *
         * @return string
         */
        public static function getColor($type)
        {
            switch ($type) {
                case self::TYPE_INFO: {
                    return self::TYPE_INFO_COLOR;
                }

                case self::TYPE_SUCCESS: {
                    return self::TYPE_SUCCESS_COLOR;
                }

                case self::TYPE_WARNING: {
                    return self::TYPE_WARNING_COLOR;
                }

                case self::TYPE_ERROR: {
                    return self::TYPE_ERROR_COLOR;
                }

                default: {
                    return self::TYPE_INFO_COLOR;
                }
            }
        }

        /**
         * @return bool|string
         */
        public static function hasFlashMessage()
        {
            if (Yii::$app->session->hasFlash(self::TYPE_ERROR)) {
                return self::TYPE_ERROR;
            } elseif (Yii::$app->session->hasFlash(self::TYPE_WARNING)) {
                return self::TYPE_WARNING;
            } elseif (Yii::$app->session->hasFlash(self::TYPE_SUCCESS)) {
                return self::TYPE_SUCCESS;
            } elseif (Yii::$app->session->hasFlash(self::TYPE_INFO)) {
                return self::TYPE_INFO;
            } else {
                return false;
            }
        }

        /**
         * @param bool $frontend
         *
         * @return void
         */
        public static function getFlashMessage($frontend = false)
        {
            $type = self::hasFlashMessage();

            if ($type !== false) {
                /** @var static[] $flashMessages */
                $flashMessages = Yii::$app->session->getFlash($type);

                echo static::write($type, $flashMessages, $frontend);
            }
        }

        /**
         * @param string $type
         * @param string $message
         * @param bool   $singleMessage
         *
         * @return void
         */
        public static function addFlashMessage($type = self::TYPE_INFO, $message = '', $singleMessage = false)
        {
            if ($singleMessage) {
                Yii::$app->session->removeAllFlashes();
            }

            Yii::$app->session->addFlash($type, new static(['type' => $type, 'message' => $message]));
        }

        /**
         * @param string $type
         * @param string $message
         * @param bool   $singleMessage
         *
         * @return void
         *
         * @depracted
         */
        public static function setFlashMessage($type = self::TYPE_INFO, $message = '', $singleMessage = false)
        {
            static::addFlashMessage($type, $message, $singleMessage);
        }

        /**
         * @return void
         */
        public static function removeAllFlashMessages()
        {
            Yii::$app->session->removeAllFlashes();
        }
    }
