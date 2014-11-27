<div id="user-profile">

	<div class="up-information">
		<div class="up-image">
			<img src="<?=$linkedin->picture_url?>" />
		</div>
		<div class="up-info">
			<div class="up-separator">Mediawiki</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Login
				</div>
				<div class="up-field-value">
					<?=$wiki['login']?>
				</div>
			</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Name
				</div>
				<div class="up-field-value">
					<?=$wiki['realname']?>
				</div>
			</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Email
				</div>
				<div class="up-field-value">
					<?=$wiki['email']?>
				</div>
			</div>

			<div class="up-separator">LinkedIn</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Name
				</div>
				<div class="up-field-value">
					<?=$linkedin->formatted_name?>
				</div>
			</div>

			<div class="up-info-field">
				<div class="up-field-title">
					ID
				</div>
				<div class="up-field-value">
					<?=$linkedin->linkedin_id?>
				</div>
			</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Headline
				</div>
				<div class="up-field-value">
					<?=$linkedin->headline?>
				</div>
			</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Industry
				</div>
				<div class="up-field-value">
					<?=$linkedin->industry?>
				</div>
			</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Summary
				</div>
				<div class="up-field-value">
					<?=$linkedin->summary?>
				</div>
			</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Specialties
				</div>
				<div class="up-field-value">
					<?=$linkedin->specialties?>
				</div>
			</div>

			<div class="up-info-field">
				<div class="up-field-title">
					Number of connections
				</div>
				<div class="up-field-value">
					<?=$linkedin->num_connections?>
				</div>
			</div>

			<div class="up-separator">Connections</div>

			<? foreach($connections as $connection): ?>
			<div class="up-info-field">
				<div class="up-field-title">
					<?=$connection->first_name?> <?=$connection->last_name?>
				</div>
				<div class="up-field-value">
					<?=$connection->headline?>
				</div>
			</div>
			<? endforeach; ?>

		</div>
	</div>

</div>