<nav class="navbar navbar-default navbar-inverse navbar-fixed-top" role="navigation">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse" aria-expanded="false">
				<span class="sr-only">Menu</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="index.php">{$gameName}</a>
		</div>

		<div class="collapse navbar-collapse navbar-ex1-collapse">
			<ul class="nav navbar-nav">
				<li><a href="index.php">{$LNG.menu_index}</a></li>
				<li><a href="index.php?page=board" target="board">{$LNG.forum}</a></li>
				<li><a href="index.php?page=news">{$LNG.menu_news}</a></li>
				<li><a href="index.php?page=rules">{$LNG.menu_rules}</a></li>
				<li><a href="index.php?page=battleHall">{$LNG.menu_battlehall}</a></li>
				<li><a href="index.php?page=banList">{$LNG.menu_banlist}</a></li>
				<li><a href="index.php?page=disclamer">{$LNG.menu_disclamer}</a></li>
			</ul>
			<ul class="nav navbar-nav navbar-right">
				<li class="nav-signup">
					<a class="btn btn-danger btn-sm navbar-btn" href="index.php?page=register">{$LNG.menu_register}</a>
				</li>
				{if count($languages) > 1}
				<li class="dropdown nav-lang">
					<button type="button" class="btn navbar-btn btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						Choose lang <span class="caret"></span>
					</button>
					<ul class="dropdown-menu dropdown-menu-right" id="language">
						{foreach $languages as $langKey => $langName}
							<li><a href="?lang={$langKey}" rel="alternate" hreflang="{$langKey}" title="{$langName}"><span class="flags {$langKey}">{$langName}</span></a></li>
						{/foreach}
					</ul>
				</li>
				{/if}
			</ul>
		</div>
	</div>
</nav>