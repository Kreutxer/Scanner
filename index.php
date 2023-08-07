<?php

class Token {
    public $type;
    public $value;
    
    public function __construct($type, $value) {
        $this->type = $type;
        $this->value = $value;
    }
}

class Lexer {
    private $input;
    private $currentPos;
    
    public function __construct($input) {
        $this->input = $input;
        $this->currentPos = 0;
    }
    
    private function isWhitespace($char) {
        return $char === ' ' || $char === "\t" || $char === "\n" || $char === "\r";
    }
    
    private function isAlpha($char) {
        return preg_match('/[a-zA-Z]/', $char);
    }
    
    private function isAlphaNumeric($char) {
        return preg_match('/[a-zA-Z0-9_]/', $char);
    }
    
    private function isDigit($char) {
        return preg_match('/[0-9]/', $char);
    }
    
    public function getNextToken() {
        if ($this->currentPos >= strlen($this->input)) {
            return null;
        }
        
        $char = $this->input[$this->currentPos];
        
        if ($this->isWhitespace($char)) {
            while ($this->currentPos < strlen($this->input) && $this->isWhitespace($this->input[$this->currentPos])) {
                $this->currentPos++;
            }
            return $this->getNextToken();
        }
        
        if ($this->isAlpha($char) || $char === '_') {
            $value = '';
            while ($this->currentPos < strlen($this->input) && $this->isAlphaNumeric($this->input[$this->currentPos])) {
                $value .= $this->input[$this->currentPos];
                $this->currentPos++;
            }
            
            // Handle keywords, identifiers, and other token types here
            $keywords = ['var', 'if', 'else', 'while', 'for', 'function', 'return', 'true', 'false', 'null', 'undefined'];
            if (in_array($value, $keywords)) {
                return new Token('KEYWORD', $value);
            } else {
                return new Token('IDENTIFIER', $value);
            }
        }
        
        if ($this->isDigit($char)) {
            $value = '';
            while ($this->currentPos < strlen($this->input) && $this->isDigit($this->input[$this->currentPos])) {
                $value .= $this->input[$this->currentPos];
                $this->currentPos++;
            }
            return new Token('NUMBER', $value);
        }
        
        // Handle operators
        $operators = [
            '+' => 'PLUS',
            '-' => 'MINUS',
            '*' => 'MULTIPLY',
            '/' => 'DIVIDE',
            '=' => 'ASSIGN',
            ';' => 'SEMICOLON',
            '(' => 'LEFT_PAREN',
            ')' => 'RIGHT_PAREN',
            '{' => 'LEFT_BRACE',
            '}' => 'RIGHT_BRACE'
            // Add more operators as needed
        ];
        
        if (array_key_exists($char, $operators)) {
            $this->currentPos++;
            return new Token($operators[$char], $char);
        }
        
        // For now, let's just return a single-character token as an example
        $this->currentPos++;
        return new Token('UNKNOWN', $char);
    }
}

$input = isset($_POST['input']) ? $_POST['input'] : '';
$tokens = [];

if (!empty($input)) {
    $lexer = new Lexer($input);
    while ($token = $lexer->getNextToken()) {
        $tokens[] = $token;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JsLex</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Javascript Lexical Scanner</h1>
        <form method="post">
            <div class="mb-3">
                <label for="input" class="form-label">Enter JavaScript Code:</label>
                <textarea class="form-control" name="input" id="input" rows="10"><?php echo $input; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Run Lexer</button>
        </form>
        
        <?php if (!empty($tokens)) : ?>
            <h2 class="mt-4">Tokens:</h2>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>Value</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tokens as $token) : ?>
                        <tr>
                            <td><?php echo $token->type; ?></td>
                            <td><?php echo $token->type === 'UNKNOWN' ? htmlentities($token->value) : $token->value; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
