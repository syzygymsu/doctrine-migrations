<?php
/*
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
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the LGPL. For more information, see
 * <http://www.doctrine-project.org>.
 */

namespace Doctrine\DBAL\Migrations\Event\Listeners;

use Doctrine\Common\EventSubscriber;
use Doctrine\DBAL\Version;
use Doctrine\DBAL\Migrations\Events;
use Doctrine\DBAL\Migrations\Event\MigrationsEventArgs;


/**
 * Listens for `onMigrationsMigrated` and, if the conneciton is has autocommit
 * makes sure to do the final commit to make sure changes stick around.
 *
 * @since 1.6
 */
final class AutoCommitListener implements EventSubscriber
{
    public function __construct()
    {
        if (Version::compare('2.5') > 0) {
            throw new \LogicException(sprintf(
                'Autocommit was introduced in DBAL 2.5, version %s detected. You cannot use %s',
                Version::VERSION,
                __CLASS__
            ));
        }
    }

    public function onMigrationsMigrated(MigrationsEventArgs $args)
    {
        $conn = $args->getConnection();
        if (!$args->isDryRun() && !$conn->isAutoCommit()) {
            $conn->commit();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getSubscribedEvents()
    {
        return [Events::onMigrationsMigrated];
    }
}
