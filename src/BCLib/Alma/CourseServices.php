<?php

namespace BCLib\Alma;

use BCLib\Alma\AlmaCache;

class CourseServices
{
    /**
     * @var AlmaSoapClient
     */
    protected $_soap_client;
    protected $_section_prototype;

    /**
     * @var \BClib\Alma\AlmaCache
     */
    protected $_cache;

    public function __construct(AlmaSoapClient $client, Section $section_prototype, AlmaCache $cache)
    {
        $this->_soap_client = $client;
        $this->_section_prototype = $section_prototype;

        $this->_cache = $cache;
    }

    public function getCourse($identifier, $from = 0, $to = 10)
    {
        $query = "searchableId=$identifier";
        return $this->_sendQuery($query, $from, $to);
    }

    /**
     * @param      $course_number
     * @param null $section_number
     * @param int  $from
     * @param int  $to
     *
     * @return Section[]
     */
    public function getCourses($course_number, $section_number = null, $from = 0, $to = 10)
    {
        if (isset($section_number)
            && $this->_cache->containsSection($course_number, $section_number)
        ) {
            return array($this->_cache->getSection($course_number, $section_number));
        }

        $query = "code=$course_number";
        if (isset($section_number)) {
            $query .= " and section=$section_number";
        }

        return $this->_sendQuery($query, $from, $to);
    }

    protected function _sendQuery($query, $from, $to)
    {
        $params = array('arg0' => $query, 'arg1' => $from, 'arg2' => $to);
        $base = $this->_soap_client->execute('searchCourseInformation', $params);
        if ($this->_soap_client->lastError() === false) {
            $sections = array();
            foreach ($base->results->course as $section_xml) {
                $section = clone $this->_section_prototype;
                $section->load($section_xml);
                $sections[] = $section;
                $cache_section = clone $section;
                $this->_cache->saveSection($cache_section);
            }
        } else {
            return false;
        }
        return $sections;
    }

    public function lastError()
    {
        return $this->_soap_client->lastError();
    }
}