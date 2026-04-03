class PaginatedResponse<T> {
  final List<T> items;
  final int totalCount;
  final int limit;
  final int offset;
  final bool hasNext;

  PaginatedResponse({
    required this.items,
    required this.totalCount,
    required this.limit,
    required this.offset,
    required this.hasNext,
  });

  factory PaginatedResponse.fromJson(
    Map<String, dynamic> json,
    T Function(Object? json) fromJsonT,
  ) {
    return PaginatedResponse<T>(
      items: (json['items'] as List).map(fromJsonT).toList(),
      totalCount: json['totalCount'] as int,
      limit: json['limit'] as int,
      offset: json['offset'] as int,
      hasNext: json['hasNext'] as bool,
    );
  }
}
