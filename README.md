####Hooks####
On registration of the new user: 

wfRunHooks('LinkedInData_profile_created', array( $profile, $friends ));
  * $profile - Model_Linkedin_profuile
  * $friends - array( 1, 3, 5 ... ) wiki userids of the guy's linkedin friends

wfRunHooks( 'LinkedInData_edit_notify', array( $article, profile, $friends ) );
  * $friends - wiki userids of the guy's linkedin friends
  * $article - article that have been edited