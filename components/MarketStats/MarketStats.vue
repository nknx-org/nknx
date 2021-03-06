<template>
  <Card :col="$mq === 'xl' ? '9' : '12'">
    <div class="market-stats">
      <div class="market-stats__data">
        <div class="market-stats__heading">
          <h3 class="market-stats__title">NKN {{$t('marketStatistic')}}</h3>
          <div class="market-stats__rank">{{$t('rank')}} {{ cmcRank }}</div>
        </div>
        <template v-if="$mq !== 'xs' && $mq !=='sm' && $mq !=='md'">
          <div class="market-stats__numbers">
            <div v-for="item in items" :key="item.title" class="market-stats__numbers-item">
              <div class="market-stats__numbers-name">{{$t(item.title)}}</div>
              <div class="market-stats__numbers-value" :class="item.class">
                <span v-if="item.icon" class="fe market-stats__numbers-icon" :class="item.icon" />
                {{ item.data }}
              </div>
            </div>
          </div>
        </template>
        <template v-else>
          <template v-if="!full">
            <div v-if="previewItems.length > 0" class="market-stats__numbers">
              <div
                v-for="item in previewItems"
                :key="item.title"
                class="market-stats__numbers-item"
              >
                <div class="market-stats__numbers-name">{{$t(item.title)}}</div>
                <div class="market-stats__numbers-value" :class="item.class">
                  <span v-if="item.icon" class="fe market-stats__numbers-icon" :class="item.icon" />
                  {{ item.data }}
                </div>
              </div>
            </div>
          </template>
          <template v-else>
            <div class="market-stats__numbers">
              <div v-for="item in items" :key="item.title" class="market-stats__numbers-item">
                <div class="market-stats__numbers-name">{{$t(item.title)}}</div>
                <div class="market-stats__numbers-value" :class="item.class">
                  <span v-if="item.icon" class="fe market-stats__numbers-icon" :class="item.icon" />
                  {{ item.data }}
                </div>
              </div>
            </div>
          </template>
          <div class="market-stats__toggle">
            <div class="market-stats__toggle-divider"></div>
            <div
              :class="['market-stats__toggle-btn', full === true ? 'fe fe-chevron-up' : 'fe fe-chevron-down']"
              @click="toggle"
            ></div>
            <div class="market-stats__toggle-divider"></div>
          </div>
        </template>
      </div>
      <div class="market-stats__chart">
        <PriceChart />
      </div>
    </div>
  </Card>
</template>

<style lang="scss">
@import './MarketStats.scss';
</style>

<script>
import { mapGetters } from 'vuex'

import Card from '~/components/Card/Card.vue'
import PriceChart from '~/components/Charts/PriceChart.vue'

export default {
  components: {
    Card,
    PriceChart
  },
  data: () => {
    return {
      cmcRank: 0,
      previewItems: [],
      full: false,
      items: {
        price: {
          title: 'price',
          data: null
        },
        change24h: {
          title: 'change24h',
          data: null
        },
        marketcap: {
          title: 'marketcap',
          data: null
        },
        dailyVolume: {
          title: 'dailyVolume',
          data: null
        },
        nknUsd: {
          title: 'NKN/USD',
          data: null
        },
        nknEth: {
          title: 'NKN/ETH',
          data: null
        }
      }
    }
  },
  computed: mapGetters({
    price: 'price/getCurrentPrice'
  }),
  destroyed() {},
  mounted: function() {
    this.items.price.data = '$' + this.price.prices[0].price.toFixed(4)

    if (this.price.prices[0].percent_change_24h > 0) {
      this.items.change24h.class = 'market-stats__numbers-value_positive'
      this.items.change24h.icon = 'fe-trending-up'
    } else {
      this.items.change24h.class = 'market-stats__numbers-value_negative'
      this.items.change24h.icon = 'fe-trending-down'
    }

    this.cmcRank = this.price.cmc_rank
    this.items.change24h.data =
      this.price.prices[0].percent_change_24h.toFixed(2) + '%'
    this.items.marketcap.data =
      '$' + this.numberWithCommas(this.price.prices[0].market_cap)
    this.items.dailyVolume.data =
      '$' + this.numberWithCommas(this.price.prices[0].volume_24h)
    this.items.nknUsd.data = this.price.prices[0].price.toFixed(7)
    this.items.nknEth.data = this.price.prices[1].price.toFixed(7)

    this.previewItems.push(this.items.price, this.items.change24h)
  },
  methods: {
    toggle() {
      this.full = !this.full
    },
    numberWithCommas(x) {
      x = x.toFixed(0)
      return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')
    }
  }
}
</script>
