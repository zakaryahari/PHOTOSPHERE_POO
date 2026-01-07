<?php
require_once 'POO.php';
require_once 'database.php';
// require_once 'Trait.php';
// require_once 'Interfaces.php';
require_once 'Repositorys.php';
$userRepo = new UserRepository();
$photoRepo = new PhotoRepository();
$albumRepo = new AlbumRepository();

// 1. Create a Pro User
$userData = [
    'id_user' => 0,
    'username' => 'Zakarya_Dev',
    'email' => 'zak@dev.com',
    'password_hash' => password_hash('123456', PASSWORD_DEFAULT),
    'bio' => 'Web Developer',
    'status' => 'active',
    'profile_picture' => 'me.jpg',
    'role' => 'pro'
];

// Assuming your ProUser entity logic exists
$user = new ProUser($userData);
$userRepo->save($user);

$u = $userRepo->findByEmail('zak@dev.com');
$uid = $u->getId();
echo "User ID created: " . $uid . "\n";

// 2. Create a Photo
$photoData = [
    'id_photo' => 0,
    'title' => 'Project Logic',
    'description' => 'Testing addPhotoToAlbum',
    'file_name' => 'logic.png',
    'file_size' => 1024,
    'mime_type' => 'image/png',
    'dimensions' => '1920x1080',
    'state' => 'published',
    'id_user' => $uid
];

$photo = new Photo($photoData);
$photoRepo->save($photo);

$pList = $photoRepo->getLatest(1);
$pid = $pList[0]->getId();
echo "Photo ID created: " . $pid . "\n";

// 3. Create an Album (Private because user is Pro)
$album = new Album([
    'id_album' => 0,
    'name' => 'My WorkSphere',
    'description' => 'Private Album',
    'is_public' => 0,
    'id_user' => $uid
]);

if ($albumRepo->save($album)) {
    echo "Album 'My WorkSphere' created successfully.\n";
}

$albums = $albumRepo->findUserAlbums($uid);
$aid = $albums[0]->getId();

// 4. Test the Bridge Table Logic
if ($albumRepo->addPhotoToAlbum($pid, $aid, $uid)) {
    echo "SUCCESS: Photo linked to Album and counter updated.\n";
}

// 5. Test Retrieval
$data = $albumRepo->getAlbumWithPhotos($aid, $uid);
echo "Photos found in album: " . count($data) . "\n";

if ($albumRepo->removePhotoFromAlbum($pid, $aid, $uid)) {
    echo "SUCCESS: Photo removed and counter decremented.\n";
}

?>