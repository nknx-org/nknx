<template>
  <div v-if="$mq !== 'md' && $mq !== 'sm' && $mq !== 'xs'" class="topbar">
    <div class="topbar__left">
      <span class="topbar__block">
        NKN/USD: ${{ $page.props.prices.usd }}
        <span
          :class="{'topbar__block_negative': $page.props.prices.usd_24h_change<0, 'topbar__block_positive': $page.props.prices.usd_24h_change>0}"
        >({{ $page.props.prices.usd_24h_change.toFixed(2) }}%)</span>
      </span>
      <span class="topbar__block">
        NKN/ETH: {{ $page.props.prices.eth }}
        <span
          :class="{'topbar__block_negative': $page.props.prices.eth_24h_change<0, 'topbar__block_positive': $page.props.prices.eth_24h_change>0}"
        >({{ $page.props.prices.eth_24h_change.toFixed(2) }}%)</span>
      </span>
    </div>
    <div class="topbar__right">
      <span class="topbar__block">
        <span class="fe fe-activity topbar__icon" />
        <span
          :class="{'topbar__block_negative': $page.props.networkStatus.syncState === 'OFFLINE'}"
        >{{ $page.props.networkStatus.syncState }}</span>
      </span>
      <span class="topbar__block">
        <span class="fe fe-layers topbar__icon" />
        {{ $page.props.networkStats.networkNodes | commaNumber}} Total Nodes
      </span>
      <span class="topbar__block">
        <span class="fe fe-git-branch topbar__icon" />
        {{ $page.props.networkStatus.networkVersion | nodeVersion }}
      </span>
    </div>
  </div>
  <div v-else class="topbar-mobile" @click="toggleTopbar">
    <div
      :class="['topbar-mobile__left', topbarExpanded === true ? 'topbar-mobile__left_hidden' : '']"
    >
      <span class="fe fe-activity topbar__icon" />
      <span class="fe fe-layers topbar__icon" />
      <span class="fe fe-git-branch topbar__icon" />
    </div>
    <div class="topbar-mobile__title">Network Stats</div>
    <span
      :class="['topbar-mobile__toggle', topbarExpanded !== true ?  'fe fe-chevron-down' : 'fe fe-chevron-up']"
    ></span>
    <ul :class="['topbar-mobile__list', topbarExpanded === true ? 'topbar-mobile__list_open' : '']">
      <li class="topbar-mobile__list-item">
        <span class="fe fe-activity topbar__icon" />
        {{ $page.props.networkStatus.syncState }}
      </li>
      <li class="topbar-mobile__list-item">
        <span class="fe fe-layers topbar__icon" />
        {{ $page.props.networkStats.networkNodes | commaNumber}} Total Nodes
      </li>
      <li class="topbar-mobile__list-item">
        <span class="fe fe-git-branch topbar__icon" />
        {{ $page.props.networkStatus.networkVersion | nodeVersion }}
      </li>
    </ul>
  </div>
</template>


<script>
import { mapGetters } from 'vuex'

export default {
  props: ['prices', 'networkStatus','networkStats'],
    data: () => ({

    }),
  computed: mapGetters({
    topbarExpanded: 'topbar/getTopbar',
  }),
  destroyed() {},

  methods: {
    toggleTopbar() {
      this.$store.dispatch('topbar/toggleTopbar')
    },
  }
}
</script>
