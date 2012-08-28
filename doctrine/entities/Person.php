<?php

/**
 * @Entity @Table(name="people")
 **/
class Person {
	/** @Id @Column(type="integer") @GeneratedValue **/
	protected $id;

	/** @Column(type="string") **/
	protected $name;

	/** @Column(type="string") **/
	protected $email;

	/** @Column(type="integer") **/
	protected $score;

	public function getId () {
		return $this->id;
	}
	
	public function setId ($id) {
		$this->id = $id;
	}

	public function getName () {
		return $this->name;
	}
	
	public function setName ($name) {
		$this->name = $name;
	}

	public function getEmail () {
		return $this->email;
	}
	
	public function setEmail ($email) {
		$this->email = $email;
	}

	public function getScore () {
		return $this->score;
	}
	
	public function setScore ($score) {
		$this->score = $score;
	}
}