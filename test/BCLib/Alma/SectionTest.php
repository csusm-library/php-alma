<?php

namespace BCLib\Alma;

require_once 'XMLLoadingTest.php';

class SectionTest extends XMLLoadingTest
{
    /**
     * @var Section
     */
    protected $_section;

    public function setUp()
    {
        $list_proto = \Mockery::mock('\BCLib\Alma\ReadingList');
        $list_proto->citations = array('foo', 'bar');

        $this->_section = new Section($list_proto);
        $this->_section->load($this->_getExampleXML('course-01.xml'));
    }

    public function testFieldsWork()
    {
        $this->assertEquals('9696528480001021', $this->_section->identifier);
        $this->assertEquals('BI110', $this->_section->code);
        $this->assertEquals('01', $this->_section->section);
        $this->assertEquals('Aging Well Test Course', $this->_section->name);
        $this->assertEquals('Biology', $this->_section->faculty);
        $this->assertEquals('ACTIVE', $this->_section->status);
        $this->assertEquals('2013-08-01Z', $this->_section->start_date);
        $this->assertEquals('2013-12-31Z', $this->_section->end_date);
        $this->assertEquals('0', $this->_section->hours);
        $this->assertEquals('O\'Neill Library Course Reserves', $this->_section->processing_department);
        $this->assertEquals('0', $this->_section->participants);
        $this->assertEquals('Lastname, Firstname', $this->_section->instructor_name);
        $this->assertEquals('LASTNA', $this->_section->instructor_username);
    }

    public function testStringArrayFieldsWork()
    {
        $terms = array('Autumn', 'Spring', 'Summer');
        $searchable_ids = array('BI010.38', 'BI010.X9');

        $this->assertEquals($terms, $this->_section->terms);
        $this->assertEquals($searchable_ids, $this->_section->searchable_ids);
    }

}