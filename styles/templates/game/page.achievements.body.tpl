{block name="title" prepend}{$LNG.achievements_name}{/block}
{block name="content"}
<div class="achievements-page">
	<table class="achievements-block">
		<tr>
			<th class="kategorie">{$LNG.achievements_badges}</th>
		</tr>
		<tr>
			<td class="achievements-badges-cell">
				<div class="achievements-badges">
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_mines}<p>{$LNG.achievements_t}<font color=yellow>{$mines_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$mines_req}</font> {$LNG.achievements_t2_1}</b>">{$mines_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_tech}<p>{$LNG.achievements_t}<font color=yellow>{$tech_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$tech_req}</font> {$LNG.achievements_t2_1}</b>">{$tech_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_engine}<p>{$LNG.achievements_t}<font color=yellow>{$engine_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$engine_req}</font> {$LNG.achievements_t2_1}</b>">{$engine_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_colonization}<p>{$LNG.achievements_t}<font color=yellow>{$colonization_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$colonization_req}</font> {$LNG.achievements_t2_1}</b>">{$colonization_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_moon}<p>{$LNG.achievements_t}<font color=yellow>{$moon_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$moon_req}</font> {$LNG.achievements_t2_1}</b>">{$moon_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_war}<p>{$LNG.achievements_t}<font color=yellow>{$war_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$war_req}</font> {$LNG.achievements_t2_1}</b>">{$war_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_destroy}<p>{$LNG.achievements_t}<font color=yellow>{$destroy_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$destroy_req}</font> {$LNG.achievements_t2_1}</b>">{$destroy_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_time}<p>{$LNG.achievements_t}<font color=yellow>{$time_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$time_req}</font> {$LNG.achievements_t2_1}</b>">{$time_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_storage}<p>{$LNG.achievements_t}<font color=yellow>{$store_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$store_req}</font> {$LNG.achievements_t2_1}</b>">{$storage_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_community}<p>{$LNG.achievements_t}<font color=yellow>{$community_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$community_req}</font> {$LNG.achievements_t2_1}</b>">{$community_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_fleet}<p>{$LNG.achievements_t}<font color=yellow>{$fleet_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$fleet_req}</font> {$LNG.achievements_t2_1}</b>">{$fleet_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_darkmatter}<p>{$LNG.achievements_t}<font color=yellow>{$darkmatter_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$darkmatter_req}</font> {$LNG.achievements_t2_1}</b>">{$darkmatter_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_planet}<p>{$LNG.achievements_t}<font color=yellow>{$planet_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$planet_req}</font> {$LNG.achievements_t2_1}</b>">{$planet_badge}</a>
					<a class="tooltip achievements-badge" href="#" data-tooltip-content="<b>{$LNG.achievements_lab}<p>{$LNG.achievements_t}<font color=yellow>{$lab_amount}</font> {$LNG.achievements_t2}<br>{$LNG.achievements_t3}<font color=yellow>{$lab_req}</font> {$LNG.achievements_t2_1}</b>">{$lab_badge}</a>
				</div>
			</td>
		</tr>
	</table>

	<table class="achievements-list">
		<tr>
			<th class="kategorie">{$LNG.achievements_name}</th>
		</tr>
		<tr>
			<td class="achievements-intro">
				<p>{$LNG.achievements_description}</p>
				<p>{$LNG.achievements_complete1}</p>
				<p>{$LNG.achievements_complete2} <span class="achievements-highlight">{$amount}</span></p>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#mines').slideToggle();return false">
					<span class="achievements-cat-title">{$mines_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$mines_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="mines" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_mines_text1} <span class="achievements-highlight">{$mines_req1}</span> {$LNG.achievements_mines_text2} <span class="achievements-highlight">{$mines_req2}</span> {$LNG.achievements_mines_text3} <span class="achievements-highlight">{$mines_req3}</span></p>
					<p class="achievements-reqs">{$LNG.achievements_mines_text4} <span class="achievements-highlight">{$mines_max_req1}</span> {$LNG.achievements_mines_text5} <span class="achievements-highlight">{$mines_max_req2}</span> {$LNG.achievements_mines_text6} <span class="achievements-highlight">{$mines_max_req3}</span></p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$mines1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$mines1_title}</p>{$LNG.achievements_mines1_desc}</b><p><b>{$mines1_req1} {$mines1_req2} {$mines1_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$mines2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$mines2_title}</p>{$LNG.achievements_mines2_desc}</b><p><b>{$mines2_req1} {$mines2_req2} {$mines2_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$mines3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$mines3_title}</p>{$LNG.achievements_mines3_desc}</b><p><b>{$mines3_req1} {$mines3_req2} {$mines3_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$mines4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$mines4_title}</p>{$LNG.achievements_mines4_desc}</b><p><b>{$mines4_req1} {$mines4_req2} {$mines4_req3}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#tech').slideToggle();return false">
					<span class="achievements-cat-title">{$tech_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$tech_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="tech" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_tech_text1} <span class="achievements-highlight">{$tech_req1}</span> {$LNG.achievements_tech_text2} <span class="achievements-highlight">{$tech_req2}</span> {$LNG.achievements_tech_text3} <span class="achievements-highlight">{$tech_req3}</span></p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$tech1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$tech1_title}</p>{$LNG.achievements_tech1_desc}</b><p><b>{$tech1_req1} {$tech1_req2} {$tech1_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$tech2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$tech2_title}</p>{$LNG.achievements_tech2_desc}</b><p><b>{$tech2_req1} {$tech2_req2} {$tech2_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$tech3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$tech3_title}</p>{$LNG.achievements_tech3_desc}</b><p><b>{$tech3_req1} {$tech3_req2} {$tech3_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$tech4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$tech4_title}</p>{$LNG.achievements_tech4_desc}</b><p><b>{$tech4_req1} {$tech4_req2} {$tech4_req3}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#engine').slideToggle();return false">
					<span class="achievements-cat-title">{$engine_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$engine_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="engine" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_engine_text1} <span class="achievements-highlight">{$engine_req1}</span> {$LNG.achievements_engine_text2} <span class="achievements-highlight">{$engine_req2}</span> {$LNG.achievements_engine_text3} <span class="achievements-highlight">{$engine_req3}</span></p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$engine1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$engine1_title}</p>{$LNG.achievements_engine1_desc}</b><p><b>{$engine1_req1} {$engine1_req2} {$engine1_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$engine2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$engine2_title}</p>{$LNG.achievements_engine2_desc}</b><p><b>{$engine2_req1} {$engine2_req2} {$engine2_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$engine3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$engine3_title}</p>{$LNG.achievements_engine3_desc}</b><p><b>{$engine3_req1} {$engine3_req2} {$engine3_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$engine4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$engine4_title}</p>{$LNG.achievements_engine4_desc}</b><p><b>{$engine4_req1} {$engine4_req2} {$engine4_req3}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#colonization').slideToggle();return false">
					<span class="achievements-cat-title">{$colonization_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$colonization_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="colonization" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_colonization_text1} <span class="achievements-highlight">{$colonization_req1}</span> {$LNG.achievements_colonization_text2}</p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$colonization1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$colonization1_title}</p>{$LNG.achievements_colonization1_desc}</b><p><b>{$colonization1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$colonization2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$colonization2_title}</p>{$LNG.achievements_colonization2_desc}</b><p><b>{$colonization2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$colonization3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$colonization3_title}</p>{$LNG.achievements_colonization3_desc}</b><p><b>{$colonization3_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#moon').slideToggle();return false">
					<span class="achievements-cat-title">{$moon_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$moon_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="moon" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_moon_text1} <span class="achievements-highlight">{$moon_req1}</span> {$LNG.achievements_moon_text2}</p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$moon1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$moon1_title}</p>{$LNG.achievements_moon1_desc}</b><p><b>{$moon1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$moon2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$moon2_title}</p>{$LNG.achievements_moon2_desc}</b><p><b>{$moon2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$moon3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$moon3_title}</p>{$LNG.achievements_moon3_desc}</b><p><b>{$moon3_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#war').slideToggle();return false">
					<span class="achievements-cat-title">{$war_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$war_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="war" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_war_text1} <span class="achievements-highlight">{$war_req1}</span> {$LNG.achievements_war_text2} <span class="achievements-highlight">{$war_req2}</span> {$LNG.achievements_war_text3}</p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$war1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$war1_title}</p>{$LNG.achievements_war1_desc}</b><p><b>{$war1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$war2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$war2_title}</p>{$LNG.achievements_war2_desc}</b><p><b>{$war2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$war3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$war3_title}</p>{$LNG.achievements_war3_desc}</b><p><b>{$war3_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#destroy').slideToggle();return false">
					<span class="achievements-cat-title">{$destroy_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$destroy_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="destroy" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_destroy_text1} <span class="achievements-highlight">{$destroy_req1}</span> {$LNG.achievements_destroy_text2} <span class="achievements-highlight">{$destroy_req2}</span> {$LNG.achievements_destroy_text3}</p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$destroy1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$destroy1_title}</p>{$LNG.achievements_destroy1_desc}</b><p><b>{$destroy1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$destroy2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$destroy2_title}</p>{$LNG.achievements_destroy2_desc}</b><p><b>{$destroy2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$destroy3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$destroy3_title}</p>{$LNG.achievements_destroy3_desc}</b><p><b>{$destroy3_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$destroy4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$destroy4_title}</p>{$LNG.achievements_destroy4_desc}</b><p><b>{$destroy4_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#time').slideToggle();return false">
					<span class="achievements-cat-title">{$time_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$time_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="time" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$time_tech}<br><br>{$time_build}<br><br>{$time_hangar}</p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$time1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$time1_title}</p>{$LNG.achievements_time1_desc}</b><p><b>{$time1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$time2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$time2_title}</p>{$LNG.achievements_time2_desc}</b><p><b>{$time2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$time3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$time3_title}</p>{$LNG.achievements_time3_desc}</b><p><b>{$time3_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#store').slideToggle();return false">
					<span class="achievements-cat-title">{$store_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$store_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="store" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_storage_text1} <span class="achievements-highlight">{$store_req1}</span> {$LNG.achievements_storage_text2} <span class="achievements-highlight">{$store_req2}</span> {$LNG.achievements_storage_text3} <span class="achievements-highlight">{$store_req3}</span></p>
					<p class="achievements-reqs">{$LNG.achievements_storage_text4} <span class="achievements-highlight">{$store_max_req1}</span> {$LNG.achievements_storage_text5} <span class="achievements-highlight">{$store_max_req2}</span> {$LNG.achievements_storage_text6} <span class="achievements-highlight">{$store_max_req3}</span></p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$store1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$store1_title}</p>{$LNG.achievements_storage1_desc}</b><p><b>{$store1_req1} {$store1_req2} {$store1_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$store2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$store2_title}</p>{$LNG.achievements_storage2_desc}</b><p><b>{$store2_req1} {$store2_req2} {$store2_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$store3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$store3_title}</p>{$LNG.achievements_storage3_desc}</b><p><b>{$store3_req1} {$store3_req2} {$store3_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$store4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$store4_title}</p>{$LNG.achievements_storage4_desc}</b><p><b>{$store4_req1} {$store4_req2} {$store4_req3}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#community').slideToggle();return false">
					<span class="achievements-cat-title">{$community_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$community_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="community" class="achievements-panel" style="display: none;">
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$community1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$community1_title}</p>{$LNG.achievements_community1_desc}</b><p><b>{$community1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$community2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$community2_title}</p>{$LNG.achievements_community2_desc}</b><p><b>{$community2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$community3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$community3_title}</p>{$LNG.achievements_community3_desc}</b><p><b>{$community3_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#fleet').slideToggle();return false">
					<span class="achievements-cat-title">{$fleet_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$fleet_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="fleet" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_fleet_text1} <span class="achievements-highlight">{$fleet_req1}</span> {$LNG.achievements_fleet_text2}</p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$fleet1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$fleet1_title}</p>{$LNG.achievements_fleet1_desc}</b><p><b>{$fleet1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$fleet2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$fleet2_title}</p>{$LNG.achievements_fleet2_desc}</b><p><b>{$fleet2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$fleet3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$fleet3_title}</p>{$LNG.achievements_fleet3_desc}</b><p><b>{$fleet3_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$fleet4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$fleet4_title}</p>{$LNG.achievements_fleet4_desc}</b><p><b>{$fleet4_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#darkmatter').slideToggle();return false">
					<span class="achievements-cat-title">{$darkmatter_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$darkmatter_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="darkmatter" class="achievements-panel" style="display: none;">
					<p class="achievements-reqs">{$LNG.achievements_darkmatter_text1} <span class="achievements-highlight">{$darkmatter_req1}</span> {$LNG.achievements_darkmatter_text2}</p>
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$darkmatter1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$darkmatter1_title}</p>{$LNG.achievements_darkmatter1_desc}</b><p><b>{$darkmatter1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$darkmatter2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$darkmatter2_title}</p>{$LNG.achievements_darkmatter2_desc}</b><p><b>{$darkmatter2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$darkmatter3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$darkmatter3_title}</p>{$LNG.achievements_darkmatter3_desc}</b><p><b>{$darkmatter3_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$darkmatter4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$darkmatter4_title}</p>{$LNG.achievements_darkmatter4_desc}</b><p><b>{$darkmatter4_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#planet').slideToggle();return false">
					<span class="achievements-cat-title">{$planet_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$planet_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="planet" class="achievements-panel" style="display: none;">
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$planet1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$planet1_title}</p>{$LNG.achievements_planet1_desc}</b><p><b>{$planet1_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$planet2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$planet2_title}</p>{$LNG.achievements_planet2_desc}</b><p><b>{$planet2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$planet3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$planet3_title}</p>{$LNG.achievements_planet3_desc}</b><p><b>{$planet3_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$planet4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$planet4_title}</p>{$LNG.achievements_planet4_desc}</b><p><b>{$planet4_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>

		<tr class="achievements-cat-row">
			<td>
				<a class="achievements-cat" href="#" onclick="$('#lab').slideToggle();return false">
					<span class="achievements-cat-title">{$lab_title}</span>
					<span class="achievements-cat-count">{$LNG.achievements_t4}{$lab_amount} {$LNG.achievements_b2}</span>
				</a>
				<div id="lab" class="achievements-panel" style="display: none;">
					<table class="achievements-items">
						<tr class="achievements-item">
							<td class="achievements-item-art">{$lab1_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$lab1_title}</p>{$LNG.achievements_lab1_desc}</b><p><b>{$lab1_req1}</b> <b>{$lab1_req2}</b> <b>{$lab1_req3}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$lab2_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$lab2_title}</p>{$LNG.achievements_lab2_desc}</b><p><b>{$lab2_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$lab3_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$lab3_title}</p>{$LNG.achievements_lab3_desc}</b><p><b>{$lab3_req1}</b></p></td>
						</tr>
						<tr class="achievements-item">
							<td class="achievements-item-art">{$lab4_img}
							<td class="achievements-item-body"><b><p class="achievements-item-title">{$lab4_title}</p>{$LNG.achievements_lab4_desc}</b><p><b>{$lab4_req1}</b></p></td>
						</tr>
					</table>
				</div>
			</td>
		</tr>
	</table>
</div>
{/block}
