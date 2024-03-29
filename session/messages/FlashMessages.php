<?php

    namespace nyx\session\messages;

    use Yii;
    use yii\base\BaseObject;

    /**
     * Flash Messages
     */
    class FlashMessages extends BaseObject
    {
        public const TYPE_INFO    = 'info';
        public const TYPE_SUCCESS = 'success';
        public const TYPE_WARNING = 'warning';
        public const TYPE_ERROR   = 'danger';

        /**
         * @var array
         */
        protected static array $titles = [
            self::TYPE_INFO    => 'Informação',
            self::TYPE_SUCCESS => 'Sucesso',
            self::TYPE_WARNING => 'Atenção',
            self::TYPE_ERROR   => 'Erro'
        ];

        /**
         * @var array
         */
        protected static array $colors = [
            self::TYPE_INFO    => '#31708f',
            self::TYPE_SUCCESS => '#3c763d',
            self::TYPE_WARNING => '#8a6d3b',
            self::TYPE_ERROR   => '#a94442'
        ];

        /**
         * @var array
         */
        protected static array $classes = [
            self::TYPE_INFO    => 'info',
            self::TYPE_SUCCESS => 'success',
            self::TYPE_WARNING => 'warning',
            self::TYPE_ERROR   => 'danger'
        ];

        /**
         * @var string
         */
        public static string $frontendMessageTemplate /** @lang HTML */ = '<div class="alert alert-{type}" role="alert">
    <h3 class="text-left text-uppercase" style="margin:0 0 5px -1px;">{title}</h3>
    <blockquote style="margin:0;border-left:5px solid {color};">
        {contents}
    </blockquote>
    <div class="clear"></div>
</div>';

        /**
         * @var string
         */
        public static string $backendMessageTemplate /** @lang HTML */ = '<div class="alert alert-{type}" role="alert">
    <h3 class="text-left text-uppercase" style="margin:0 0 5px -1px;">{title}</h3>
    <blockquote style="margin:0;border-left:5px solid {color};">
        {contents}
    </blockquote>
    <div class="clear"></div>
</div>';

        /**
         * @param string      $type
         * @param array|mixed $messages
         * @param bool        $frontend
         * @param bool        $forceNoList
         *
         * @return string
         *
         * @noinspection PhpUnusedLocalVariableInspection
         */
        public static function write(string $type, mixed $messages, bool $frontend = false, bool $forceNoList = true): string
        {
            $title    = static::getTitle($type);
            $color    = static::getColor($type);
            $contents = '';
            $useList  = false;

            if (is_array($messages)) {
                if (count($messages) > 1) {
                    $useList = !$forceNoList;
                }

                foreach ($messages as $line) {
                    if (!$line instanceof static || property_exists($line, 'message')) {
                        continue;
                    }

                    /** @noinspection PhpUndefinedFieldInspection */
                    $contents .= (($useList) ? '<li style="font-size:14px;font-weight:normal;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> ' : '').$line->message.(($useList) ? '</li>' : '');
                }
            } else {
                return '';
            }

            if (!empty($contents)) {
                $contents = (($useList) ? '<ul class="list-unstyled">' : '<p style="font-size:14px;font-weight:normal;"><span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> ').$contents.(($useList) ? '</ul>' : '</p>');
            } else {
                return '';
            }

            $templateVariables = ['type', 'title', 'color', 'contents'];
            $template          = static::$backendMessageTemplate;

            if ($frontend) {
                $template = static::$frontendMessageTemplate;
            }

            foreach ($templateVariables as $variable) {
                $template = preg_replace('/{'.$variable.'}/', $$variable, $template);
            }

            return $template;
        }

        /**
         * @return array
         */
        public static function getAvailableTypes(): array
        {
            return [self::TYPE_INFO, self::TYPE_SUCCESS, self::TYPE_WARNING, self::TYPE_ERROR];
        }

        /**
         * @param string $type
         *
         * @return string
         */
        public static function getTitle(string $type): string
        {
            switch ($type) {
                case self::TYPE_SUCCESS: {
                    return static::$titles[self::TYPE_SUCCESS];
                }

                case self::TYPE_WARNING: {
                    return static::$titles[self::TYPE_WARNING];
                }

                case self::TYPE_ERROR: {
                    return static::$titles[self::TYPE_ERROR];
                }

                case self::TYPE_INFO:
                default: {
                    return static::$titles[self::TYPE_INFO];
                }
            }
        }

        /**
         * @param string $type
         *
         * @return string
         */
        public static function getColor(string $type): string
        {
            switch ($type) {
                case self::TYPE_SUCCESS: {
                    return static::$colors[self::TYPE_SUCCESS];
                }

                case self::TYPE_WARNING: {
                    return static::$colors[self::TYPE_WARNING];
                }

                case self::TYPE_ERROR: {
                    return static::$colors[self::TYPE_ERROR];
                }

                case self::TYPE_INFO:
                default: {
                    return static::$colors[self::TYPE_INFO];
                }
            }
        }

        /**
         * @return bool|string
         */
        public static function hasFlashMessage(): bool|string
        {
            if (Yii::$app->session->hasFlash(self::TYPE_ERROR)) {
                return self::TYPE_ERROR;
            }

            if (Yii::$app->session->hasFlash(self::TYPE_WARNING)) {
                return self::TYPE_WARNING;
            }

            if (Yii::$app->session->hasFlash(self::TYPE_SUCCESS)) {
                return self::TYPE_SUCCESS;
            }

            if (Yii::$app->session->hasFlash(self::TYPE_INFO)) {
                return self::TYPE_INFO;
            }

            return false;
        }

        /**
         * @param bool $frontend
         *
         * @return void
         */
        public static function getFlashMessage(bool $frontend = false): void
        {
            $type = static::hasFlashMessage();

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
        public static function addFlashMessage(string $type = self::TYPE_INFO, string $message = '', bool $singleMessage = false): void
        {
            if ($singleMessage) {
                Yii::$app->session->removeAllFlashes();
            }

            Yii::$app->session->addFlash($type, new static(compact('type', 'message')));
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
        public static function setFlashMessage(string $type = self::TYPE_INFO, string $message = '', bool $singleMessage = false): void
        {
            static::addFlashMessage($type, $message, $singleMessage);
        }

        /**
         * @return void
         */
        public static function removeAllFlashMessages(): void
        {
            Yii::$app->session->removeAllFlashes();
        }

        /**
         * @param string $type
         * @param string $color
         *
         * @return bool
         */
        public static function changeColor(string $type, string $color): bool
        {
            if (isset(static::$colors[$type])) {
                static::$colors[$type] = $color;

                return true;
            }

            return false;
        }

        /**
         * @param string $type
         * @param string $title
         *
         * @return bool
         */
        public static function changeTitle(string $type, string $title): bool
        {
            if (isset(static::$titles[$type])) {
                static::$titles[$type] = $title;

                return true;
            }

            return false;
        }

        /**
         * @param string $type
         * @param string $class
         *
         * @return bool
         */
        public static function changeClass(string $type, string $class): bool
        {
            if (isset(static::$classes[$type])) {
                static::$classes[$type] = $class;

                return true;
            }

            return false;
        }
    }
