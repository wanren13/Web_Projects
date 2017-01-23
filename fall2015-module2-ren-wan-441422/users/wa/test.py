# from util import *
# import util

# def f1(self, x, y) :
# 	return min(x, y)

# class MyClass:
#     """A simple example class"""
#     i = 12345
#     def f(ssss):
#         return MyClass.i


# c = MyClass()

# print c.f()

#     f2 = f1

# c = MyClass()

# print c.f()

# print dir(c)

# print globals()

# print vars(c)

# print c.f2(20, 10)



# class Bag:
#     def __init__(s0):
#         s0.data = []
#     def add(s1, x):
#         s1.data.append(x)
#     def addtwice(s2, x):
#         s2.add(x)
#         s2.add(x)

#     def __f(self):
#     	return 2

#     def f1(self):
#    		return self.__f()

# b = Bag()

# b.add(2)

# str = "Hello"

# print str + ' [Default: %default]'





# b = Bag()

# b.addtwice(3)

# print b.data

# print dir(b)

# print b._Bag__f();


# class B:
#     pass
# class C(B):
#     pass
# class D(C):
#     pass

# for c in [B, C, D]:
#     try:
#         raise c()
#     except D:
#         print "D"
#     except C:
#         print "C"
#     except B:
#         print "B"


# s = [((5, 4), 'South', 1), ((4, 5), 'West', 1)]
# print [x[0] for x in s]

# a = [(5, 4), (5, 5), (4, 5), (3, 5), (1, 5), (1, 4), (2, 3), (2, 2), (1, 1)]
# if a:
# 	print a[::-1]
# else:
# 	print "a is empty"

# a = {(2,5): (3,4)}

# print a[(2,5)]

# import heapq

# class PriorityQueue:
#     """
#       Implements a priority queue data structure. Each inserted item
#       has a priority associated with it and the client is usually interested
#       in quick retrieval of the lowest-priority item in the queue. This
#       data structure allows O(1) access to the lowest-priority item.

#       Note that this PriorityQueue does not allow you to change the priority
#       of an item.  However, you may insert the same item multiple times with
#       different priorities.
#     """
#     def  __init__(self):
#         self.heap = []

#     def push(self, item, priority):
#         pair = (priority,item)
#         heapq.heappush(self.heap,pair)

#     def pop(self):
#         (priority,item) = heapq.heappop(self.heap)
#         return item

#     def isEmpty(self):
#         return len(self.heap) == 0

# pq = PriorityQueue()

# pq.push(" Hello ", 2)
# pq.push(" World ", 1)

# print pq.pop()

# dict = {2:"Haha"}
# dict[1] = "Haha"
# # print dict[2]

# if 1 not in dict or dict[1] != "Haha":
# 	print "Yes"
# else:
# 	print "No"

# 	

a = (2,1)

print a[1]