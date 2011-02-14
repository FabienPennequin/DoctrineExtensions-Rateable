# Doctrine Extensions Rateable

This repository contains the rateable extension for Doctrine 2. This allows to
rate your doctrine entities easily.


## Use

Each rate is a tuple consisting of a resource id, a user id and a score.


### Implement the DoctrineExtensions\Rateable\Rateable interface.

First, your entity must implement the `DoctrineExtensions\Rateable\Rateable` interface.
Five methods in your entity must be written:

 * `getId()`
 * `getRatingVotes()`
 * `setRatingVotes($number)`
 * `getRatingTotal()`
 * `setRatingTotal($number)`

Example:

    namespace MyProject;
    use DoctrineExtensions\Rateable\Rateable;

    /**
     * @Entity
     */
    class Article implements Rateable
    {
        /**
         * @Id
         * @GeneratedValue
         * @Column(type="integer")
         */
        protected $id;

        /**
         * @Column(name="rating_votes", type="integer")
         */
        protected $ratingVotes = 0;

        /**
         * @Column(name="rating_total", type="integer")
         */
        protected $ratingTotal = 0;


        public function getId()
        {
            return $this->id;
        }

        public function getRatingVotes()
        {
            return $this->ratingVotes;
        }

        public function setRatingVotes($number)
        {
            $this->ratingVotes = $number;
        }

        public function getRatingTotal()
        {
            return $this->ratingTotal;
        }

        public function setRatingTotal($number)
        {
            $this->ratingTotal = $number;
        }
    }


### Implement the DoctrineExtensions\Rateable\User interface.

Second, your user entity must implement `DoctrineExtensions\Rateable\User` interface.
Four methods are needed:

 * `getId()`
 * `canAddRate(Rateable $resource)`
 * `canChangeRate(Rateable $resource)`
 * `canRemoveRate(Rateable $resource)`

Example:

    namespace MyProject;
    use DoctrineExtensions\Rateable\Rateable;
    use DoctrineExtensions\Rateable\User as RateableUser;

    /**
     * @Entity
     */
    class User implements RateableUser
    {
        /**
         * @Id
         * @GeneratedValue
         * @Column(type="integer")
         */
        protected $id;

        public function getId()
        {
            return $this->id;
        }

        public function canAddRate(Rateable $resource)
        {
            return true;
        }

        public function canChangeRate(Rateable $resource)
        {
            return true;
        }

        public function canRemoveRate(Rateable $resource)
        {
            return false;
        }
    }


### Setup RatingListener

    // $this->em = EntityManager::create($connection, $config);
    // ...

    $this->em->getEventManager()->addEventSubscriber(new RatingListener());


### Using RatingManager

Now, you can use RatingManager.

    $ratingManager = new RatingManager($em);

    // Add a new rate..
    $ratingManager->addRate($resource, $user, 4);

    // Change my rate..
    $ratingManager->changeRate($resource, $user, 2);

    // Remove my rate..
    try {
        $ratingManager->removeRate($resource, $user);
    } catch (PermissionDeniedException $e) {
        echo 'Oh, no permission to remove my rate!';
    }


    // Compute resource rating score...
    $ratingManager->getRatingScore($resource); // will return 2


### Exceptions

Rateable extension can throw four different exceptions:

 * `InvalidRateScoreException` when your rate score is not between 1 and 5
 * `NotFoundRateException` when your try to change or remove an non existant rate
 * `PermissionDeniedException` when user have not permission to do this action
 * `ResourceAlreadyRatedException` when user have already rate the resource and you try to add new one

