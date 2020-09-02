<?php

namespace Ethinking\PushConnectorBundle\Service;

use Symfony\Component\Mime\Encoder\Base64ContentEncoder;
use Symfony\Component\Mime\Encoder\ContentEncoderInterface;
use Symfony\Component\Mime\Encoder\EightBitContentEncoder;
use Symfony\Component\Mime\Encoder\QpContentEncoder;
use Symfony\Component\Mime\Exception\InvalidArgumentException;
use Symfony\Component\Mime\Header\Headers;
use Symfony\Component\Mime\Part\AbstractPart;

class JsonPart extends AbstractPart
{
    private static $encoders = [];

    private $body;
    private $charset;
    private $subtype;
    private $disposition;
    private $name;
    private $encoding;

    const QUOTED_PRINTABLE = 'quoted-printable';
    const CONTENT_TYPE = 'Content-Type';
    const CONTENT_DISPOSITION = 'Content-Disposition';


    /**
     * @param $body
     * @param string|null $charset
     * @param string $subtype
     * @param string|null $encoding
     */
    public function __construct(array $body, ?string $charset = 'utf-8', $subtype = 'json', string $encoding = null)
    {
        parent::__construct();

        if (!is_array($body)) {
            throw new \TypeError(sprintf('The body of "%s" must be an array (got "%s").',
                self::class, is_object($body) ? get_class($body) : gettype($body)));
        }

        $this->body = json_encode($body);
        $this->charset = $charset;
        $this->subtype = $subtype;

        if (null === $encoding) {
            $this->encoding = $this->chooseEncoding();
        } else {
            if (self::QUOTED_PRINTABLE !== $encoding && 'base64' !== $encoding && '8bit' !== $encoding) {
                throw new InvalidArgumentException(sprintf('The encoding must be one of "quoted-printable", 
                "base64", or "8bit" ("%s" given).', $encoding));
            }
            $this->encoding = $encoding;
        }
    }

    public function getMediaType(): string
    {
        return 'application';
    }

    public function getMediaSubtype(): string
    {
        return $this->subtype;
    }

    /**
     * @param string $disposition one of attachment, inline, or form-data
     *
     * @return $this
     */
    public function setDisposition(string $disposition)
    {
        $this->disposition = $disposition;

        return $this;
    }

    /**
     * Sets the name of the file (used by FormDataPart).
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    public function getBody(): string
    {
        if (!is_resource($this->body)) {
            return $this->body;
        }

        if (stream_get_meta_data($this->body)['seekable'] ?? false) {
            rewind($this->body);
        }

        return stream_get_contents($this->body) ?: '';
    }

    public function bodyToString(): string
    {
        return $this->getEncoder()->encodeString($this->getBody(), $this->charset);
    }

    public function bodyToIterable(): iterable
    {
        if (\is_resource($this->body)) {
            if (stream_get_meta_data($this->body)['seekable'] ?? false) {
                rewind($this->body);
            }
            yield from $this->getEncoder()->encodeByteStream($this->body);
        } else {
            yield $this->getEncoder()->encodeString($this->body);
        }
    }

    public function getPreparedHeaders(): Headers
    {
        $headers = parent::getPreparedHeaders();

        $headers->setHeaderBody('Parameterized', self::CONTENT_TYPE,
            $this->getMediaType() . '/' . $this->getMediaSubtype());
        if ($this->charset) {
            $headers->setHeaderParameter(self::CONTENT_TYPE, 'charset', $this->charset);
        }
        if ($this->name) {
            $headers->setHeaderParameter(self::CONTENT_TYPE, 'name', $this->name);
        }
        $headers->setHeaderBody('Text', 'Content-Transfer-Encoding', $this->encoding);

        if (!$headers->has(self::CONTENT_DISPOSITION) && null !== $this->disposition) {
            $headers->setHeaderBody('Parameterized', self::CONTENT_DISPOSITION, $this->disposition);
            if ($this->name) {
                $headers->setHeaderParameter(self::CONTENT_DISPOSITION, 'name', $this->name);
            }
        }

        return $headers;
    }

    public function asDebugString(): string
    {
        $str = parent::asDebugString();
        if (null !== $this->charset) {
            $str .= ' charset: ' . $this->charset;
        }
        if (null !== $this->disposition) {
            $str .= ' disposition: ' . $this->disposition;
        }

        return $str;
    }

    private function getEncoder(): ContentEncoderInterface
    {
        if ('8bit' === $this->encoding) {
            return self::$encoders[$this->encoding] ??
                (self::$encoders[$this->encoding] = new EightBitContentEncoder());
        }

        if (self::QUOTED_PRINTABLE === $this->encoding) {
            return self::$encoders[$this->encoding] ?? (self::$encoders[$this->encoding] = new QpContentEncoder());
        }

        return self::$encoders[$this->encoding] ?? (self::$encoders[$this->encoding] = new Base64ContentEncoder());
    }

    private function chooseEncoding(): string
    {
        if (null === $this->charset) {
            return 'base64';
        }

        return self::QUOTED_PRINTABLE;
    }

    /**
     * @return array
     */
    public function __sleep()
    {
        // convert resources to strings for serialization
        if (is_resource($this->body)) {
            $this->body = $this->getBody();
        }

        $this->_headers = $this->getHeaders();

        return ['_headers', 'body', 'charset', 'subtype', 'disposition', 'name', 'encoding'];
    }

    public function __wakeup()
    {
        $r = new \ReflectionProperty(AbstractPart::class, 'headers');
        $r->setAccessible(true);
        $r->setValue($this, $this->_headers);
        unset($this->_headers);
    }
}
