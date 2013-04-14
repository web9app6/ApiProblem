<?php

namespace Crell\ApiProblem;

/**
 * An API error of some form.
 *
 * This object generates errors in compliance with the IETF api-problem
 * specification draft.
 *
 * This object should be configured via the appropriate methods, and then
 * rendered using the asJson() or asXml() methods. The resulting string is
 * safe to then send in response to an HTTP request. When sent, the response
 * should have a mime type of application/api-problem+json or
 * application/api-problem+xml, as appropriate.
 *
 * Subclassing this class to provide defaults for different problem types for
 * your application is encouraged.
 *
 * For problem properties defined by the specification, use the methods provided
 * to get/set those values. For extended values, use the ArrayAccess interface
 * to specify arbitrary additional properties.
 *
 * @link http://tools.ietf.org/html/draft-nottingham-http-problem-03
 */
class ApiProblem implements \ArrayAccess
{

    /**
     *  A short, human-readable summary of the problem type.
     *
     *  It SHOULD NOT change from occurrence to occurrence of the problem,
     *  except for purposes of localisation.
     *
     * @var string
     */
    protected $title;

    /**
     * An absolute URI [RFC3986] that identifies the problem type.
     *
     * When dereferenced, it SHOULD provide human-readable documentation for the
     * problem type (e.g., using HTML).
     *
     * @var string
     */
    protected $problemType;

    /**
     * The HTTP status code set by the origin server for this occurrence of the problem.
     *
     * The httpStatus member, if present, is only advisory; it conveys the HTTP
     * status code used for the convenience of the consumer. Generators MUST
     * use the same status code in the actual HTTP response, to assure that
     * generic HTTP software that does not understand this format still behaves
     * correctly.
     *
     * @var int
     */
    protected $httpStatus;

    /**
     * An human readable explanation specific to this occurrence of the problem.
     *
     * The detail member, if present, SHOULD focus on helping the client correct
     * the problem, rather than giving debugging information.
     *
     * Consumers SHOULD NOT be parse the detail member for information;
     * extensions are more suitable and less error-prone ways to obtain such
     * information.
     *
     * @var string
     */
    protected $detail;

    /**
     * An absolute URI that identifies the specific occurrence of the problem.
     *
     * It may or may not yield further information if dereferenced.
     *
     * @var string
     */
    protected $problemInstance;

    /**
     * Any arbitrary extension properties that have been assigned on this object.
     *
     * @var array
     */
    protected $extensions = array();

    /**
     *
     * @param type $title
     *   A short, human-readable summary of the problem type.  It SHOULD NOT
     *   change from occurrence to occurrence of the problem, except for
     *   purposes of localisation.
     * @param type $type
     *   An absolute URI [RFC3986] that identifies the problem type.  When
     *   dereferenced, it SHOULD provide human-readable documentation for the
     *   problem type (e.g., using HTML).
     */
    public function __construct($title = '', $type = '')
    {
        $this->title = $title;
        $this->problemType = $type;
        $this->detail = '';
        $this->problemInstance = '';
    }

    /**
     * Retrieves the title of the problem.
     *
     * @return string
     *   The current title.
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title for this problem.
     *
     * @param string $title
     *   The title to set.
     *  @return \Crell\ApiProblem\ApiProblem
     *   The invoked object.
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Retrieves the problem type of this problem.
     *
     * @return string
     *   The problem type URI of this problem.
     */
    public function getProblemType()
    {
        return $this->problemType;
    }

    /**
     * Sets the problem type of this problem.
     *
     * @param string $type
     *   The resolvable problem type URI of this problem.
     * @return \Crell\ApiProblem\ApiProblem
     *   The invoked object.
     */
    public function setProblemType($type)
    {
        $this->problemType = $type;
        return $this;
    }

    /**
     * Retrieves the detail information of the problem.
     *
     * @return string
     *   The detail of this problem.
     */
    public function getDetail()
    {
        return $this->detail;
    }

    /**
     * Sets the detail for this problem.
     *
     * @param string $detail
     *   The human-readable detail string about this problem.
     * @return \Crell\ApiProblem\ApiProblem
     *   The invoked object.
     */
    public function setDetail($detail)
    {
        $this->detail = $detail;
        return $this;
    }

    /**
     * Returns the problem instance URI of this problem.
     *
     * @return string
     *   The problem instance URI of this problem.
     */
    public function getProblemInstance()
    {
        return $this->problemInstance;
    }

    /**
     * Sets the problem instance URI of this problem.
     *
     * @param string $problemInstance
     *   An absolute URI that uniquely identifies this problem. It MAY link to
     *   further information about the error, but that is not required.
     *
     * @return \Crell\ApiProblem\ApiProblem
     *   The invoked object.
     */
    public function setProblemInstance($problemInstance)
    {
        $this->problemInstance = $problemInstance;
        return $this;
    }

    /**
     * Returns the current HTTP status code.
     *
     * @return int|null
     *   The current HTTP status code. If not set, it will return NULL.
     */
    public function getHttpStatus()
    {
        return $this->httpStatus;
    }

    /**
     * Sets the HTTP status code for this problem.
     *
     * It is an error for this value to be set to a different value than the
     * actual HTTP response code.
     *
     * @param int $status
     *   A valid HTTP status code.
     * @return \Crell\ApiProblem\ApiProblem
     *   The invoked object.
     */
    public function setHttpStatus($status)
    {
        $this->httpStatus = $status;
        return $this;
    }

    public function asJson($pretty)
    {
        $response = $this->compile();

        $options = 0;
        if (version_compare(PHP_VERSION, '5.4.0') >= 0 && $pretty) {
            $options = JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT;
        }

        return json_encode($response, $options);
    }

    public function asXml()
    {
        throw new \Exception("Not yet implemented.");

    }

    protected function compile()
    {
        // Start with any extensions, since that's already an array.
        $response = $this->extensions;

        // These properties are required.  If they're not set, it's an error.
        $response['title'] = $this->title;
        $response['problemType'] = $this->problemType;

        // These properties are optional.
        $response['httpStatus'] = $this->httpStatus;
        $response['detail'] = $this->detail;
        $response['problemInstance'] = $this->problemInstance;

        return $response;
    }


    /**
     * {@inheritdoc}
     */
    public function offsetExists($offset)
    {
        return array_key_exists($offset, $this->extensions);
    }

    /**
     * {@inheritdoc}
     */
    public function offsetGet($offset)
    {
        return $this->extensions[$offset];
    }

    /**
     * {@inheritdoc}
     */
    public function offsetSet($offset, $value)
    {
        $this->extensions[$offset] = $value;
    }

    /**
     * {@inheritdoc}
     */
    public function offsetUnset($offset)
    {
        unset($this->extensions[$offset]);
    }

}