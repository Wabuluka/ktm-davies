import { FC } from 'react';
import { Pages } from '../Types';
import { PageList } from './PageList';
import { useSites } from '@/Features/Site/Hooks/useSites';
import { useEditingBanner } from '@/Features/Banner/Hooks/useEditingBanner';

export const SitePageList: FC = () => {
  const editingBanner = useEditingBanner();
  const sites = useSites();
  const pages: Pages = sites.map((site) => ({
    name: site.name,
    children: [
      {
        name: 'Site Page',
        children: site.pages?.map((page) => ({
          name: page.title,
          href: route('sites.pages.edit', { site: site.id, page: page.id }),
          isActive: route().current('sites.pages.*', {
            site: site.id,
            page: page.id,
          }),
        })),
      },
      {
        name: 'News',
        href: route('sites.news.index', { site: site.id }),
        isActive: route().current('sites.news.index', { site: site.id }),
      },
      {
        name: 'News Category',
        href: route('sites.news-categories.index', site),
        isActive: route().current('sites.news-categories.index', site),
      },
      {
        name: 'Banner',
        children: site.bannerPlacements?.map((placement) => ({
          name: placement.name,
          href: route('banner-placements.banners.index', {
            banner_placement: placement.id,
          }),
          isActive:
            route().current('banner-placements.banners.*', {
              banner_placement: placement.id,
            }) || editingBanner?.placement_id == placement.id,
        })),
      },
    ],
  }));

  return <PageList pages={pages} />;
};
