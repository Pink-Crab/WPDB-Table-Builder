<?php 

declare(strict_types=1);

/**
 * Schema definition
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @since 0.3.0
 * @author Glynn Quelch <glynn.quelch@gmail.com>
 * @license http://www.opensource.org/licenses/mit-license.html  MIT License
 * @package PinkCrab\Table_Builder
 */

namespace PinkCrab\Table_Builder;

class Column {

    /**
     * Column name
     *
     * @var string
     */
    protected $name;

    /**
     * The column type
     *
     * @var string|null
     */
    protected $type = null;

    /**
     * The column length
     *
     * @var int|null
     */
    protected $length = null;

    /**
     * Denotes if the column is nullable
     *
     * @var bool|null
     */
    protected $nullable = null;

    /**
     * The columns default value
     *
     * @var string|null
     */
    protected $default = null;

    /**
     * If the column has the auto incrememnt flag.
     *
     * @var bool|null
     */
    protected $auto_increment = null;

    /**
     * Is the columns value unsigned
     *
     * @var bool|null
     */
    protected $usigned = null;

    public function __construct(string $name) {
        $this->name = $name;
    }

    /**
     * Sets the columns type
     *
     * @param string $type
     * @return self
     */
    public function type(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Sets the column length
     *
     * @param integer $length
     * @return self
     */
    public function length(int $length): self
    {
        $this->length = $length;
        return $this;
    }

    /**
     * Denotes if the column is nullable
     *
     * @param boolean $nullable
     * @return self
     */
    public function nullable(bool $nullable = true): self
    {
        $this->nullable = $nullable;
        return $this;
    }

    /**
     * Sets the default value
     *
     * @param string $default
     * @return self
     */
    public function default(string $default): self
    {
        $this->default = $default;
        return $this;
    }

    /**
     * Denotes if the column is unsigned.
     *
     * @param boolean $unsigned
     * @return self
     */
    public function unsigned(bool $unsigned): self
    {
        $this->default = $default;
        return $this;
    }
}