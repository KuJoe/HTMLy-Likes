# HTMLy Likes
A simple script for adding a "Like" button on [HTMLy](https://github.com/danpros/htmly) posts. It uses basic IP, sessions, and cookie tracking to ensure a user can only like a post once.

![An animated GIF showing a mouse cursor clicking on an empty star with a 0 next to it and the star gets filled in and the 0 turns into a 1.](https://a.cdn9000.com/IPtLWOJfYzoVbT4a/htmly_like.gif)

## Installation
1) Upload the following files to your domain's root directory:
  * like.js
  * like_api.php
  * likes_dashboard.php

2) Copy the following code\* into the **post.html.php** for the theme you are using in HTMLy (i.e. /themes/twentysixteen/post.html.php):
```
<!-- /Like Widget Start/ -->
<div class="like-widget" data-url="<?php echo $p->url; ?>" style="display:flex;justify-content:center;align-items:center;margin:18px 0;">
	<button class="like-btn" style="background:none;border:none;outline:none;cursor:pointer;font-size:1.5em;">
		<span class="star-icon" style="color:#FFD700;">☆</span>
	</button>
	<span class="like-count" style="margin:5px 0 0 5px;">0</span>
</div>
<script src="<?php echo site_url();?>like.js"></script>
<!-- /Like Widget End/ -->
```

*\*Placement and CSS can be edited to best fit your theme.*

## Updating
1) Overwrite the following files to your domain's root directory:
  * like.js
  * like_api.php
  * likes_dashboard.php
2) Navigate to: `/like_api.php?update=1`

## likes_dashboard.php
This is a simple dashboard with a basic table of every post that's receieved at least one like and shows the count. It is only accessible by users who are logged in.