Error in RefreshTokenManager
Since Doctrine Common 3.0, Doctrine\Common\Persistence\ObjectManager became Doctrine\Persistence\ObjectManager
so to be able to get JWT tokens to authenticate you will need to adjust this manually
