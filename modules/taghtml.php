<?php

class tagHtml
{
    private string $tag = "";
    private string $value = "";
    private array $atributes = [];
    private bool $isUnique = false;

    public function setTag(string $tag, bool $isUnique = false) {
        $this->tag = $tag;
        if (isset($isUnique) && $isUnique == true) {
            $this->isUnique = $isUnique;
        }
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function addAtribute($atribute_name, $atribute_value) {
        if (isset($atribute_name) && isset($atribute_value)) {
            $this->atributes[$atribute_name] = $atribute_value;
        }
    }

    public function mount() {
        $atributes_mounted = "";

        foreach ($this->atributes as $key => $value) {
            $atributes_mounted .= " $key=\"$value\"";
        }
        
        if ($this->isUnique) {
            $mounted = "<{$this->tag}{$atributes_mounted}>";
        } else {
            $mounted = "<{$this->tag}{$atributes_mounted}>{$this->value}</{$this->tag}>";
        }

        return $mounted;
    }
}