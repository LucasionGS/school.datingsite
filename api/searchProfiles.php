<?php
require_once("../src/p/Models.php");
function searchProfiles() {
  $ps = new ProfileSearch($_REQUEST);
  // 0-based index for page specification.
  $page = -1;
  if (isset($_REQUEST["page"])) {
    if (is_numeric($_REQUEST["page"])) $page = $_REQUEST["page"];
    unset($_REQUEST["page"]);
  }
  $profiles = Profile::searchProfiles($ps, $page);

  if ($profiles != null) return new Result(true, null, $profiles);
  else return new Result(true, "No profiles found.", $profiles);
}
echo json_encode(searchProfiles());