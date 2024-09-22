export const createPaginationLinks = (url: string, count: number) => {
  const tmpUrl = new URL(url);

  const page = tmpUrl.searchParams.get('page');
  const currentIndex: number = page ? Number(page) - 1 : 0;

  const pagenationLinks: string[] = [
    ...[...Array(count)].map((_, i) => {
      const searchParams = tmpUrl.searchParams;

      searchParams.set('page', String(i + 1));
      tmpUrl.search = searchParams.toString();

      const pageLink = tmpUrl.toString();

      return pageLink;
    }),
  ];

  return {
    currentIndex,
    pagenationLinks: pagenationLinks,
  };
};
